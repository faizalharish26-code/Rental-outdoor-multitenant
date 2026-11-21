<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        ]);

        return view('tenant.orders.show', [
            'tenant' => $tenant,
            'order' => $order,
        ]);
    }
}
