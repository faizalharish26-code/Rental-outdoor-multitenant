<?php

namespace App\Http\Controllers;

use App\Models\InventoryUnit;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TenantOrderController extends Controller
{
    private const STATUSES = [
        'pending',
        'paid',
        'fulfilled',
        'completed',
        'cancelled',
    ];

    private const TRANSITIONS = [
        'pending' => ['cancelled'],
        'paid' => ['fulfilled'],
        'fulfilled' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    private const RETURN_CONDITIONS = ['good', 'fair', 'damaged'];

    public function index(Request $request, Tenant $tenant): View
    {
        Gate::authorize('viewAny', [Product::class, $tenant]);

        $status = (string) $request->query('status', '');

        if (! in_array($status, self::STATUSES, true)) {
            $status = '';
        }

        $orders = $tenant->orders()
            ->with('user')
            ->when(
                $status !== '',
                fn ($query) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tenant.orders.index', [
            'tenant' => $tenant,
            'orders' => $orders,
            'selectedStatus' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(Tenant $tenant, Order $order): View
    {
        Gate::authorize('viewAny', [Product::class, $tenant]);

        abort_unless(
            (int) $order->tenant_id === (int) $tenant->id,
            404
        );

        $order->load([
            'user',
            'items.product.category',
            'items.inventoryUnit',
        ]);

        return view('tenant.orders.show', [
            'tenant' => $tenant,
            'order' => $order,
        ]);
    }

    public function updateStatus(
        Request $request,
        Tenant $tenant,
        Order $order
    ): RedirectResponse {
        Gate::authorize('viewAny', [Product::class, $tenant]);

        abort_unless(
            (int) $order->tenant_id === (int) $tenant->id,
            404
        );

        $allowedStatuses = self::TRANSITIONS[$order->status] ?? [];
        $rules = [
            'status' => [
                'required',
                'string',
                Rule::in($allowedStatuses),
            ],
        ];
        $returnItems = collect();

        if ($request->input('status') === 'completed') {
            $returnItems = $order->items()
                ->with('inventoryUnit')
                ->get();

            if ($returnItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'returns' => 'Pesanan tanpa rincian barang tidak dapat diselesaikan.',
                ]);
            }

            foreach ($returnItems as $item) {
                if (! $item->inventoryUnit) {
                    throw ValidationException::withMessages([
                        'returns' => 'Setiap barang harus memiliki unit inventaris sebelum pesanan diselesaikan.',
                    ]);
                }
            }

            $rules['returns'] = ['required', 'array', 'size:'.$returnItems->count()];

            foreach ($returnItems as $item) {
                $conditionField = 'returns.'.$item->getKey().'.condition';
                $noteField = 'returns.'.$item->getKey().'.note';

                $rules[$conditionField] = [
                    'required',
                    'string',
                    Rule::in(self::RETURN_CONDITIONS),
                ];
                $rules[$noteField] = [
                    Rule::requiredIf(
                        fn (): bool => $request->input($conditionField) === 'damaged'
                    ),
                    'nullable',
                    'string',
                    'max:2000',
                ];
            }
        }

        $validated = $request->validate($rules);

        if ($validated['status'] === 'completed') {
            $expectedItemIds = $returnItems
                ->map(fn ($item): string => (string) $item->getKey())
                ->sort()
                ->values()
                ->all();
            $providedItemIds = array_map('strval', array_keys($validated['returns']));
            sort($providedItemIds);

            if ($providedItemIds !== $expectedItemIds) {
                throw ValidationException::withMessages([
                    'returns' => 'Data pemeriksaan harus sesuai dengan semua barang pada pesanan.',
                ]);
            }
        }

        DB::transaction(function () use ($tenant, $order, $validated): void {
            $lockedOrder = $tenant->orders()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $nextStatuses = self::TRANSITIONS[$lockedOrder->status] ?? [];

            if (! in_array($validated['status'], $nextStatuses, true)) {
                throw ValidationException::withMessages([
                    'status' => 'Perubahan status ini sudah tidak berlaku. Muat ulang halaman.',
                ]);
            }

            if ($validated['status'] === 'completed') {
                $items = $lockedOrder->items()
                    ->lockForUpdate()
                    ->get();
                $unitIds = $items->pluck('inventory_unit_id');

                if (
                    $items->isEmpty()
                    || $unitIds->contains(null)
                    || $unitIds->unique()->count() !== $items->count()
                ) {
                    throw ValidationException::withMessages([
                        'returns' => 'Setiap barang harus terhubung ke satu unit inventaris yang berbeda.',
                    ]);
                }

                foreach ($items as $item) {
                    $unit = InventoryUnit::query()
                        ->whereKey($item->inventory_unit_id)
                        ->where('tenant_id', $tenant->id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (! $unit) {
                        throw ValidationException::withMessages([
                            'returns' => 'Unit inventaris pesanan tidak ditemukan pada toko ini.',
                        ]);
                    }

                    $returnData = $validated['returns'][$item->getKey()] ?? null;

                    if (! $returnData) {
                        throw ValidationException::withMessages([
                            'returns' => 'Data pemeriksaan harus diisi untuk semua unit.',
                        ]);
                    }

                    $damaged = $returnData['condition'] === 'damaged';

                    $unit->update([
                        'condition' => $returnData['condition'],
                        'status' => $damaged ? 'maintenance' : 'available',
                        'maintenance_note' => $damaged
                            ? trim($returnData['note'])
                            : null,
                        'maintenance_started_at' => $damaged ? today() : null,
                    ]);
                }
            }

            $lockedOrder->update([
                'status' => $validated['status'],
            ]);
            if ($validated['status'] === 'cancelled') {
                $lockedOrder->update(['payment_status' => 'cancelled']);
                $lockedOrder->payment()
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);
            }
        });

        $messages = [
            'paid' => 'Pembayaran pesanan sudah dikonfirmasi.',
            'fulfilled' => 'Pesanan sudah ditandai diserahkan kepada pelanggan.',
            'completed' => 'Pengembalian dicatat dan status setiap unit inventaris diperbarui.',
            'cancelled' => 'Pesanan dibatalkan. Unit inventaris dapat dipesan kembali pada periode tersebut.',
        ];

        return to_route('tenant.orders.show', [$tenant, $order])
            ->with('success', $messages[$validated['status']]);
    }
}
