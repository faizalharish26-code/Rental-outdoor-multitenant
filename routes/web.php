<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CheckoutForm;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/checkout', CheckoutForm::class)->name('checkout');
});

require __DIR__.'/settings.php';
