<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Seller\CustomArchitecturalProductController;
use App\Http\Controllers\Seller\CustomOrderController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ReadyMadeOrderController;
use App\Http\Controllers\Seller\TaxonomyController;
use App\Http\Controllers\Seller\VillaDesignController;
use Illuminate\Support\Facades\Route;

// Authentication & Demo Role Switcher (Public)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/login/quick/{email}', [LoginController::class, 'quickLogin'])->name('quick.login');
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Root Route: If logged in, go to orders; if not logged in, redirect strictly to Login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('seller.orders.index');
    }
    return redirect()->route('login');
});

// Seller / Workshop Management Dashboard: Strictly Protected by Auth Middleware
// Unauthenticated users cannot view any orders, details, quotes, or production stages
Route::prefix('seller')->name('seller.')->middleware('auth')->group(function () {
    // 1. Custom Orders & Quotes
    Route::get('/orders', [CustomOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/quote', [CustomOrderController::class, 'quote'])->name('orders.quote');
    Route::post('/orders/{order}/accept', [CustomOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/reject', [CustomOrderController::class, 'reject'])->name('orders.reject');
    Route::post('/orders/{order}/simulate-advance', [CustomOrderController::class, 'simulateAdvancePayment'])->name('orders.simulate_advance');
    Route::post('/orders/{order}/stage', [CustomOrderController::class, 'updateStage'])->name('orders.stage');

    // 2. Ready-Made Product Sales & Order List
    Route::get('/orders/ready-made', [ReadyMadeOrderController::class, 'index'])->name('orders.ready_made');
    Route::post('/orders/ready-made/{order}/status', [ReadyMadeOrderController::class, 'updateStatus'])->name('orders.ready_made.status');

    // 3. Products Catalog Management
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/create-custom', [ProductController::class, 'createCustom'])->name('products.create_custom');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // 3b. Dedicated Custom Architectural Products Management & Serials
    Route::get('/custom-products', [CustomArchitecturalProductController::class, 'index'])->name('custom_products.index');
    Route::post('/custom-products/reorder', [CustomArchitecturalProductController::class, 'reorder'])->name('custom_products.reorder');
    Route::post('/custom-products/{product}/serial', [CustomArchitecturalProductController::class, 'updateSerial'])->name('custom_products.serial');
    Route::delete('/custom-products/{product}', [CustomArchitecturalProductController::class, 'destroy'])->name('custom_products.destroy');

    // 4. Taxonomy: Categories & Subcategories
    Route::get('/categories', [TaxonomyController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [TaxonomyController::class, 'storeCategory'])->name('categories.store');
    Route::post('/subcategories', [TaxonomyController::class, 'storeSubcategory'])->name('subcategories.store');

    // 5. Taxonomy: Brands / Studios
    Route::get('/brands', [TaxonomyController::class, 'brands'])->name('brands.index');
    Route::post('/brands', [TaxonomyController::class, 'storeBrand'])->name('brands.store');

    // 6. Villa Architectural Designs & Custom Showcase (Majlis, Thermal Windows, Partitions, Sofas)
    Route::resource('villa-designs', VillaDesignController::class)->except(['show']);
});

// Direct public storage file serving fallback (fixes broken images if public/storage symlink is missing or on Windows/Docker/Render)
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        // Fallback to high quality architectural image if file is missing (e.g. ephemeral container restart)
        return redirect('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80');
    }

    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=86400',
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('path', '.*');

