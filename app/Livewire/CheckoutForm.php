<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class CheckoutForm extends Component
{
    public int|string|null $tenant_id = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    /**
     * @var array<int, array{product_id: int|string, quantity: int}>
     */
    public array $items = [];

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'tenant_id' => 'required',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
    ];

    public function submitOrder(): void
    {
        $this->validate();

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $days = max(1, $start->diffInDays($end));

        DB::transaction(function () use ($days): void {
            $totalAmount = 0.0;
            $itemsData = [];

            foreach ($this->items as $item) {
                /** @var Product $product */
                $product = Product::findOrFail($item['product_id']);
                $subtotal = (float) $product->rental_price_per_day * (int) $item['quantity'] * $days;
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => (int) $item['quantity'],
                    'price_per_day' => (float) $product->rental_price_per_day,
                    'subtotal' => $subtotal,
                ];
            }

            /** @var Order $order */
            $order = Order::create([
                'user_id' => Auth::id(),
                'tenant_id' => $this->tenant_id,
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);

            foreach ($itemsData as $data) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product_id'],
                    'inventory_unit_id' => null,
                    'quantity' => $data['quantity'],
                    'price_per_day' => $data['price_per_day'],
                    'subtotal' => $data['subtotal'],
                ]);
            }

            session()->flash('message', 'Order berhasil dibuat! ID: '.$order->id);
            $this->reset(['items', 'start_date', 'end_date']);
        });
    }

    public function render(): View
    {
        return view('components.checkout-form', [
            'products' => Product::where('tenant_id', $this->tenant_id)->get(),
        ]);
    }
}
