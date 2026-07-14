<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\StockOutController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\ShippingAddressController;
use App\Http\Controllers\Customer\ShopController;
use App\Http\Controllers\Api\RajaOngkirController;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========================================
// PUBLIC ROUTES (Bisa diakses tanpa login)
// ========================================

// Halaman Home
Route::get('/', function () {
    // Get products with stock > 0, order by most order items
    $products = Product::with('category')
        ->where('stock', '>', 0)
        ->withCount('orderItems')
        ->orderByDesc('order_items_count')
        ->latest()
        ->take(8)
        ->get();
        
    $categories = Category::where('status', 'active')->get();
    return view('home', compact('products', 'categories'));
})->name('home');

// Halaman Shop & Detail Produk (Publik)
Route::get('/shop', [ShopController::class, 'index'])->name('customer.shop.index');
Route::get('/products/{product}', [ShopController::class, 'show'])->name('customer.shop.show');

// Redirect Superiority & Contact ke Landing Page
Route::get('/superiority', function () {
    return redirect()->route('home');
})->name('superiority');

Route::get('/contact', function () {
    return redirect()->route('home');
})->name('contact');

// Google OAuth Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


// ========================================
// CUSTOMER ROUTES (Wajib Login & Role Customer)
// ========================================
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {

    // Hapus route /shop dan /products dari sini karena sudah dipindah ke atas
    
    // Cart / Keranjang (Wajib login kalau mau masukin barang)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/bulk-action', [CartController::class, 'bulkAction'])->name('cart.bulk-action');

    // Checkout & Orders (Wajib login)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/orders', function () {
        return view('customer.orders.index');
    })->name('orders.index');

    Route::get('/orders/{order}', function ($order) {
        // Load order dengan relasi
        $order = Order::with(['user', 'shippingAddress', 'orderItems.product', 'payment'])
            ->findOrFail($order);

        // Cek ownership
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('customer.orders.show', compact('order'));
    })->name('orders.show');

    Route::post('/orders/{order}/payment', [CheckoutController::class, 'uploadPayment'])->name('orders.payment');
    Route::post('/orders/{order}/cancel', [CheckoutController::class, 'cancel'])->name('orders.cancel');

    // Shipping Addresses
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [ShippingAddressController::class, 'index'])->name('index');
        Route::get('/create', [ShippingAddressController::class, 'create'])->name('create');
        Route::post('/', [ShippingAddressController::class, 'store'])->name('store');
        Route::get('/{shippingAddress}/edit', [ShippingAddressController::class, 'edit'])->name('edit');
        Route::put('/{shippingAddress}', [ShippingAddressController::class, 'update'])->name('update');
        Route::delete('/{shippingAddress}', [ShippingAddressController::class, 'destroy'])->name('destroy');
    });

    // RajaOngkir API (AJAX endpoints untuk dependent dropdown & ongkir)
    Route::prefix('api/rajaongkir')->name('api.rajaongkir.')->group(function () {
        Route::get('/provinces', [RajaOngkirController::class, 'provinces'])->name('provinces');
        Route::get('/cities/{provinceId}', [RajaOngkirController::class, 'cities'])->name('cities');
        Route::get('/districts/{cityId}', [RajaOngkirController::class, 'districts'])->name('districts');
        Route::get('/subdistricts/{districtId}', [RajaOngkirController::class, 'subdistricts'])->name('subdistricts');
        Route::post('/cost', [RajaOngkirController::class, 'cost'])->name('cost');
    });

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
});

// ========================================
// ADMIN ROUTES (Auth + Admin Role)
// ========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Redirect /admin to /admin/dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories (Resource Controller)
    Route::resource('categories', CategoryController::class);

    // Products (Resource Controller)
    Route::resource('products', ProductController::class);

    // Route::resource('suppliers', SupplierController::class);

    // Stock In
    Route::resource('stock-ins', StockInController::class)->except(['show']);

    // Stock Out
    Route::resource('stock-outs', StockOutController::class)->except(['show']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('orders.verifyPayment');

    // Reports
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/export-stock', [ReportController::class, 'exportStock'])->name('reports.exportStock');
    Route::get('/reports/export-sales', [ReportController::class, 'exportSales'])->name('reports.exportSales');

    // Site Settings (Edit Website)
    Route::get('/site-settings', [SiteSettingController::class, 'index'])->name('site-settings.index');
    Route::put('/site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
});

// ========================================
// FALLBACK ROUTE
// ========================================
Route::fallback(function () {
    return view('errors.404');
});

require __DIR__.'/auth.php';