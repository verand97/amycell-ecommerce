<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ───────────────────────────────────────────────────────────
Route::get('/', [Customer\HomeController::class, 'index'])->name('home');
Route::get('/catalog', [Customer\HomeController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{product:slug}', [Customer\HomeController::class, 'showProduct'])->name('catalog.show');

// Cart count (AJAX, no auth needed)
Route::get('/cart/count', [Customer\CartController::class, 'count'])->name('cart.count');

// ─── Customer Auth Routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Cart
    Route::prefix('cart')->name('customer.cart')->group(function () {
        Route::get('/', [Customer\CartController::class, 'index'])->name('');
        Route::post('/add/{productId}', [Customer\CartController::class, 'add'])->name('.add');
        Route::patch('/update/{productId}', [Customer\CartController::class, 'update'])->name('.update');
        Route::delete('/remove/{productId}', [Customer\CartController::class, 'remove'])->name('.remove');
        Route::post('/clear', [Customer\CartController::class, 'clear'])->name('.clear');
    });

    // Checkout
    Route::prefix('checkout')->name('customer.checkout')->group(function () {
        Route::get('/', [Customer\CheckoutController::class, 'index'])->name('');
        Route::post('/', [Customer\CheckoutController::class, 'store'])->name('.store');
        Route::get('/success/{orderNumber}', [Customer\CheckoutController::class, 'success'])->name('.success');
        Route::post('/upload-payment/{order}', [Customer\CheckoutController::class, 'uploadPayment'])->name('.upload-payment');
    });

    // Orders
    Route::prefix('orders')->name('customer.orders')->group(function () {
        Route::get('/', [Customer\OrderController::class, 'index'])->name('');
        Route::get('/{order}', [Customer\OrderController::class, 'show'])->name('.show');
        Route::post('/{order}/cancel', [Customer\OrderController::class, 'cancel'])->name('.cancel');
    });

    // Chat (API-style for JS integration)
    Route::prefix('chat')->name('customer.chat')->group(function () {
        Route::post('/start', [Customer\ChatController::class, 'startSession'])->name('.start');
        Route::post('/session/{session}/message', [Customer\ChatController::class, 'sendMessage'])->name('.send');
        Route::get('/session/{session}/messages', [Customer\ChatController::class, 'getMessages'])->name('.messages');
        Route::get('/session/{session}/status', [Customer\ChatController::class, 'getSessionStatus'])->name('.status');
    });

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Products CRUD
    Route::resource('products', Admin\ProductController::class);
    Route::post('/products/{product}/toggle-status', [Admin\ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');

    // Categories CRUD
    Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // Orders
    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/update-status', [Admin\TransactionController::class, 'updateOrderStatus'])
        ->name('orders.update-status');

    // Transactions
    Route::get('/transactions', [Admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/{transaction}/verify', [Admin\TransactionController::class, 'verify'])
        ->name('transactions.verify');
    Route::post('/transactions/{transaction}/reject', [Admin\TransactionController::class, 'reject'])
        ->name('transactions.reject');

    // Chat Dashboard (FIFO)
    Route::get('/chat', [Admin\ChatController::class, 'dashboard'])->name('chat.dashboard');
    Route::post('/chat/session/{session}/accept', [Admin\ChatController::class, 'acceptSession'])
        ->name('chat.accept');
    Route::post('/chat/session/{session}/message', [Admin\ChatController::class, 'sendMessage'])
        ->name('chat.send');
    Route::get('/chat/session/{session}/messages', [Admin\ChatController::class, 'getMessages'])
        ->name('chat.messages');
    Route::post('/chat/session/{session}/close', [Admin\ChatController::class, 'closeSession'])
        ->name('chat.close');
    Route::get('/chat/queue', [Admin\ChatController::class, 'getQueue'])
        ->name('chat.queue');
});

require __DIR__.'/auth.php';
