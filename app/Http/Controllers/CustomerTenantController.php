<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerTenantIndexRequest;
use App\Models\Tenant;
use Illuminate\View\View;

class CustomerTenantController extends Controller
{
    public function index(CustomerTenantIndexRequest $request): View
    {
        $search = $request->validated('q');

        $tenants = Tenant::query()
            ->where('status', 'active') // Hanya tenant aktif
            ->when($search, function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('customer.tenants.index', [
            'tenants' => $tenants,
            'search' => $search,
        ]);
    }
}