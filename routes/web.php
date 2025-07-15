<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/shop');

Route::get('/shop', [ProductController::class, 'shop'])->name('shop');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'totalProducts' => \App\Models\Product::count(),
        'lowStockProducts' => \App\Models\Product::where('quantity', '<', 5)->get(),
        'totalOrders' => \App\Models\Order::count(),
        'recentOrders' => \App\Models\Order::with(['user', 'status'])->latest()->take(5)->get(),
        'totalRevenue' => (float) \App\Models\Order::sum('total'),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('orders', \App\Http\Controllers\OrderController::class);
});

require __DIR__.'/auth.php';
