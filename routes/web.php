<?php

use App\Http\Controllers\TenantProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('tenant/{tenant:slug}')
        ->name('tenant.')
        ->scopeBindings()
        ->group(function (): void {
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
        });
});

require __DIR__.'/settings.php';