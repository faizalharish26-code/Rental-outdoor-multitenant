<?php

namespace App\Http\Controllers;

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
        'pending' => ['paid', 'cancelled'],
        'paid' => ['fulfilled'],
        'fulfilled' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

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

        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in($allowedStatuses),
            ],
        ]);

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

            $lockedOrder->update([
                'status' => $validated['status'],
            ]);
        });

        $messages = [
            'paid' => 'Pembayaran pesanan sudah dikonfirmasi.',
            'fulfilled' => 'Pesanan sudah ditandai diserahkan kepada pelanggan.',
            'completed' => 'Pesanan sudah ditandai selesai dan barang dikembalikan.',
            'cancelled' => 'Pesanan dibatalkan. Unit inventaris dapat dipesan kembali pada periode tersebut.',
        ];

        return to_route('tenant.orders.show', [$tenant, $order])
            ->with('success', $messages[$validated['status']]);
    }
}
