<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCategoryRequest;
use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TenantCategoryController extends Controller
{
    public function index(Tenant $tenant): View
    {
        Gate::authorize('viewAny', [Category::class, $tenant]);

        $categories = $tenant->categories()
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15);

        return view('tenant.categories.index', [
            'tenant' => $tenant,
            'categories' => $categories,
        ]);
    }

    public function create(Tenant $tenant): View
    {
        Gate::authorize('create', [Category::class, $tenant]);

        return view('tenant.categories.form', [
            'tenant' => $tenant,
            'category' => new Category,
        ]);
    }

    public function store(
        SaveCategoryRequest $request,
        Tenant $tenant
    ): RedirectResponse {
        $tenant->categories()->create($request->validated());

        return to_route('tenant.categories.index', $tenant)
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Tenant $tenant, Category $category): View
    {
        abort_unless(
            (int) $category->tenant_id === (int) $tenant->id,
            404
        );

        Gate::authorize('update', $category);

        return view('tenant.categories.form', [
            'tenant' => $tenant,
            'category' => $category,
        ]);
    }

    public function update(
        SaveCategoryRequest $request,
        Tenant $tenant,
        Category $category
    ): RedirectResponse {
        abort_unless(
            (int) $category->tenant_id === (int) $tenant->id,
            404
        );

        $category->update($request->validated());

        return to_route('tenant.categories.index', $tenant)
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(
        Tenant $tenant,
        Category $category
    ): RedirectResponse {
        abort_unless(
            (int) $category->tenant_id === (int) $tenant->id,
            404
        );

        Gate::authorize('delete', $category);

        if ($category->products()->withTrashed()->exists()) {
            return to_route('tenant.categories.index', $tenant)
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk, termasuk produk yang sudah dihapus sementara.');
        }

        try {
            $category->deleteOrFail();
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) !== 1451) {
                throw $exception;
            }

            return to_route('tenant.categories.index', $tenant)
                ->with('error', 'Kategori tidak dapat dihapus karena sudah digunakan oleh data lain.');
        }

        return to_route('tenant.categories.index', $tenant)
            ->with('success', 'Kategori berhasil dihapus.');
    }
}