<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TenantDashboardController extends Controller
{
    public function __invoke(Tenant $tenant): View
    {
        Gate::authorize('viewAny', [Product::class, $tenant]);

        $productCount = $tenant->products()->count();

        $availableProductCount = $tenant->products()
            ->where('is_available', true)
            ->count();

        $categoryCount = $tenant->categories()->count();

        $pendingOrderCount = Order::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->count();

        $recentProducts = $tenant->products()
            ->with('category')
            ->latest()
            ->limit(3)
            ->get();

        return view('tenant.dashboard', [
            'tenant' => $tenant,
            'productCount' => $productCount,
            'availableProductCount' => $availableProductCount,
            'categoryCount' => $categoryCount,
            'pendingOrderCount' => $pendingOrderCount,
            'recentProducts' => $recentProducts,
        ]);
    }
}
