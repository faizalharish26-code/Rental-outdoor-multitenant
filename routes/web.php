<?php

use App\Http\Controllers\TenantCategoryController;
use App\Http\Controllers\TenantDashboardController;
use App\Http\Controllers\TenantOrderController;
use App\Http\Controllers\TenantProductController;
use App\Livewire\CheckoutForm;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Route tenant: dashboard admin, produk, dan kategori
    Route::prefix('tenant/{tenant:slug}')
        ->name('tenant.')
        ->scopeBindings()
        ->group(function (): void {
            Route::get('/dashboard', TenantDashboardController::class)
                ->name('dashboard');

            Route::get('/orders', [TenantOrderController::class, 'index'])
                ->name('orders.index');

            Route::get('/orders/{order}', [TenantOrderController::class, 'show'])
                ->name('orders.show');

            Route::get('/products', [TenantProductController::class, 'index'])
                ->name('products.index');

            Route::get('/products/create', [TenantProductController::class, 'create'])
                ->name('products.create');

            Route::post('/products', [TenantProductController::class, 'store'])
                ->name('products.store');

            Route::get('/products/{product}/edit', [TenantProductController::class, 'edit'])
                ->name('products.edit');

            Route::put('/products/{product}', [TenantProductController::class, 'update'])
                ->name('products.update');

            Route::delete('/products/{product}', [TenantProductController::class, 'destroy'])
                ->name('products.destroy');

            Route::get('/categories', [TenantCategoryController::class, 'index'])
                ->name('categories.index');

            Route::get('/categories/create', [TenantCategoryController::class, 'create'])
                ->name('categories.create');

            Route::post('/categories', [TenantCategoryController::class, 'store'])
                ->name('categories.store');

            Route::get('/categories/{category}/edit', [TenantCategoryController::class, 'edit'])
                ->name('categories.edit');

            Route::put('/categories/{category}', [TenantCategoryController::class, 'update'])
                ->name('categories.update');

            Route::delete('/categories/{category}', [TenantCategoryController::class, 'destroy'])
                ->name('categories.destroy');
        });

    // Route checkout
    Route::get('/checkout', CheckoutForm::class)->name('checkout');
});

require __DIR__.'/settings.php';
