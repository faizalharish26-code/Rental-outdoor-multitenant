<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogIndexRequest;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\View\View;

class CustomerCatalogController extends Controller
{
    public function index(CatalogIndexRequest $request, Tenant $tenant): View
    {
        // Tolak jika tenant tidak aktif
        abort_if($tenant->status !== 'active', 404);

        $search = $request->validated('q');
        $categoryId = $request->validated('category');

        // Ambil kategori milik tenant aktif saja
        $categories = $tenant->categories()
            ->orderBy('name')
            ->get();

        // Tolak filter kategori jika ID kategori bukan milik tenant ini (mencegah lintas tenant)
        if ($categoryId !== null && ! $categories->contains('id', (int) $categoryId)) {
            abort(404);
        }

        // Mulai query langsung dari relasi $tenant->products()
        $products = $tenant->products()
            ->with(['category']) // Tambahkan 'images' jika Wisnu sudah menyediakan relasinya
            ->where('is_available', true) // Hanya produk yang tampil di katalog
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($search, function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('customer.catalog.index', [
            'tenant' => $tenant,
            'categories' => $categories,
            'products' => $products,
            'search' => $search,
            'selectedCategory' => $categoryId,
        ]);
    }

    public function show(Tenant $tenant, Product $product): View
    {
        // Pastikan tenant aktif
        abort_if($tenant->status !== 'active', 404);

        // Pastikan produk benar-benar milik tenant ini & tersedia di katalog
        abort_if((int) $product->tenant_id !== (int) $tenant->id, 404);
        abort_if(! $product->is_available, 404);

        $product->load(['category']);

        return view('customer.catalog.show', [
            'tenant' => $tenant,
            'product' => $product,
        ]);
    }
}