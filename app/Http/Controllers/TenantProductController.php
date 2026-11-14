<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProductRequest;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantProductController extends Controller
{
    public function index(Tenant $tenant): View
    {
        abort_unless(
            auth()->user()->can('viewAny', [Product::class, $tenant]),
            403
        );

        $products = $tenant->products()
            ->with('category')
            ->orderBy('name')
            ->paginate(20);

        return view('tenant.products.index', [
            'tenant' => $tenant,
            'products' => $products,
        ]);
    }

    public function create(Tenant $tenant): View
    {
        abort_unless(
            auth()->user()->can('create', [Product::class, $tenant]),
            403
        );

        return view('tenant.products.form', [
            'tenant' => $tenant,
            'product' => new Product(['is_available' => true]),
            'categories' => $tenant->categories()->orderBy('name')->get(),
        ]);
    }

    public function store(
        SaveProductRequest $request,
        Tenant $tenant
    ): RedirectResponse {
        $tenant->products()->create($request->validated());

        return to_route('tenant.products.index', $tenant)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Tenant $tenant, Product $product): View
    {
        abort_unless(
            auth()->user()->can('update', $product),
            403
        );

        abort_unless(
            (int) $product->tenant_id === (int) $tenant->id,
            404
        );

        return view('tenant.products.form', [
            'tenant' => $tenant,
            'product' => $product,
            'categories' => $tenant->categories()->orderBy('name')->get(),
        ]);
    }

    public function update(
        SaveProductRequest $request,
        Tenant $tenant,
        Product $product
    ): RedirectResponse {
        abort_unless(
            (int) $product->tenant_id === (int) $tenant->id,
            404
        );

        $product->update($request->validated());

        return to_route('tenant.products.index', $tenant)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(
        Tenant $tenant,
        Product $product
    ): RedirectResponse {
        abort_unless(
            (int) $product->tenant_id === (int) $tenant->id,
            404
        );

        abort_unless(
            auth()->user()->can('delete', $product),
            403
        );

        $product->delete();

        return to_route('tenant.products.index', $tenant)
            ->with('success', 'Produk berhasil dihapus.');
    }
}
