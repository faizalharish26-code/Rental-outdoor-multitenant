<?php

namespace App\Livewire;

use App\Models\InventoryUnit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CheckoutForm extends Component
{
    public int|string|null $tenant_id = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    /**
     * @var array<int, array{product_id: int|string, quantity: int|string}>
     */
    public array $items = [];

    protected function rules(): array
    {
        return [
            'tenant_id' => [
                'required',
                'integer',
                Rule::exists('tenants', 'id')->where('status', 'active'),
            ],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:30'],
        ];
    }

    public function updatedTenantId(): void
    {
        $this->items = [];
        $this->resetErrorBag('items');
    }

    public function addProduct(int $productId): void
    {
        if (! $this->tenant_id) {
            $this->addError('tenant_id', 'Pilih toko terlebih dahulu.');

            return;
        }

        $product = Product::query()
            ->where('tenant_id', $this->tenant_id)
            ->where('is_available', true)
            ->find($productId);

        if (! $product) {
            $this->addError('items', 'Produk tidak tersedia di toko yang dipilih.');

            return;
        }

        $existingIndex = collect($this->items)
            ->search(fn (array $item): bool => (int) $item['product_id'] === $product->id);

        if ($existingIndex !== false) {
            $this->items[$existingIndex]['quantity'] =
                (int) $this->items[$existingIndex]['quantity'] + 1;

            return;
        }

        $this->items[] = [
            'product_id' => $product->id,
            'quantity' => 1,
        ];
    }

    public function removeProduct(int $productId): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            fn (array $item): bool => (int) $item['product_id'] !== $productId
        ));
    }

    public function submitOrder(): void
    {
        $validated = $this->validate();

        $tenantId = (int) $validated['tenant_id'];
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $days = max(1, (int) ceil($start->diffInMinutes($end) / 1440));

        $requestedItems = collect($validated['items'])
            ->groupBy(fn (array $item): int => (int) $item['product_id'])
            ->map(fn ($rows): array => [
                'product_id' => (int) $rows->first()['product_id'],
                'quantity' => (int) $rows->sum(
                    fn (array $item): int => (int) $item['quantity']
                ),
            ])
            ->sortBy('product_id')
            ->values();

        $order = DB::transaction(function () use (
            $tenantId,
            $start,
            $end,
            $days,
            $requestedItems
        ): Order {
            $tenant = Tenant::query()
                ->whereKey($tenantId)
                ->where('status', 'active')
                ->first();

            if (! $tenant) {
                throw ValidationException::withMessages([
                    'tenant_id' => 'Toko tidak ditemukan atau sedang tidak aktif.',
                ]);
            }

            $selectedUnits = [];
            $totalAmount = 0;

            foreach ($requestedItems as $requestedItem) {
                $product = Product::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('is_available', true)
                    ->whereKey($requestedItem['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu produk tidak tersedia di toko ini.',
                    ]);
                }

                $units = InventoryUnit::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('product_id', $product->id)
                    ->where('status', 'available')
                    ->whereDoesntHave('orderItems', function ($itemQuery) use ($start, $end): void {
                        $itemQuery->whereHas('order', function ($orderQuery) use ($start, $end): void {
                            $orderQuery
                                ->whereIn('status', ['pending', 'paid', 'fulfilled'])
                                ->where('start_date', '<', $end)
                                ->where('end_date', '>', $start);
                        });
                    })
                    ->orderBy('unit_code')
                    ->lockForUpdate()
                    ->limit($requestedItem['quantity'])
                    ->get();

                if ($units->count() < $requestedItem['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Unit {$product->name} tidak cukup tersedia pada tanggal sewa tersebut.",
                    ]);
                }

                foreach ($units as $unit) {
                    $selectedUnits[] = [
                        'product' => $product,
                        'unit' => $unit,
                    ];

                    $totalAmount += (float) $product->rental_price_per_day * $days;
                }
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'start_date' => $start,
                'end_date' => $end,
            ]);

            foreach ($selectedUnits as $selected) {
                $product = $selected['product'];
                $unit = $selected['unit'];
                $pricePerDay = (float) $product->rental_price_per_day;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'inventory_unit_id' => $unit->id,
                    'quantity' => 1,
                    'price_per_day' => $pricePerDay,
                    'subtotal' => $pricePerDay * $days,
                ]);
            }

            return $order;
        });

        session()->flash(
            'message',
            "Pesanan {$order->order_number} berhasil dibuat. Unit sudah ditahan untuk pesanan ini."
        );

        $this->reset(['items', 'start_date', 'end_date']);
    }

    public function render(): View
    {
        $products = collect();
        $selectedProducts = collect();

        if ($this->tenant_id) {
            $products = Product::query()
                ->where('tenant_id', $this->tenant_id)
                ->where('is_available', true)
                ->withCount([
                    'inventoryUnits as ready_units_count' => fn ($query) => $query
                        ->where('status', 'available'),
                ])
                ->orderBy('name')
                ->get();

            $selectedIds = collect($this->items)
                ->filter(fn ($item): bool => is_array($item) && isset($item['product_id']))
                ->pluck('product_id')
                ->filter(fn ($id): bool => is_numeric($id))
                ->unique()
                ->values();

            $selectedProducts = Product::query()
                ->where('tenant_id', $this->tenant_id)
                ->whereIn('id', $selectedIds)
                ->get()
                ->keyBy('id');
        }

        return view('livewire.checkout-form', [
            'tenants' => Tenant::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
            'products' => $products,
            'selectedProducts' => $selectedProducts,
        ]);
    }
}