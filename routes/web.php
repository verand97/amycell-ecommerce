<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ───────────────────────────────────────────────────────────
Route::get('/', [Customer\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');
Route::get('/catalog', [Customer\HomeController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{product:slug}', [Customer\HomeController::class, 'showProduct'])->name('catalog.show');

// Service HP (public landing)
Route::get('/service', [Customer\ServiceController::class, 'landing'])->name('service.landing');

// Cart count (AJAX, no auth needed)
Route::get('/cart/count', [Customer\CartController::class, 'count'])->name('cart.count');

// Midtrans Webhook Notification
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');

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

    // Service HP
    Route::prefix('service')->name('customer.service')->group(function () {
        Route::get('/my', [Customer\ServiceController::class, 'index'])->name('');
        Route::get('/create', [Customer\ServiceController::class, 'create'])->name('.create');
        Route::post('/', [Customer\ServiceController::class, 'store'])->name('.store');
        Route::get('/{serviceOrder}', [Customer\ServiceController::class, 'show'])->name('.show');
        Route::post('/{serviceOrder}/approve', [Customer\ServiceController::class, 'approve'])->name('.approve');
        Route::post('/{serviceOrder}/cancel', [Customer\ServiceController::class, 'cancel'])->name('.cancel');
        Route::post('/{serviceOrder}/pay-cash', [Customer\ServiceController::class, 'payCash'])->name('.pay-cash');
        Route::post('/{serviceOrder}/pay-transfer', [Customer\ServiceController::class, 'payTransfer'])->name('.pay-transfer');
        Route::post('/{serviceOrder}/pay-midtrans', [Customer\ServiceController::class, 'payMidtrans'])->name('.pay-midtrans');
    });

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [Admin\NotificationController::class, 'markAllRead'])->name('notifications.mark-read');
    Route::get('/notifications/{notification}/read', [Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');

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

    // Services (Servis HP)
    Route::get('/services', [Admin\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{serviceOrder}', [Admin\ServiceController::class, 'show'])->name('services.show');
    Route::post('/services/{serviceOrder}/update-status', [Admin\ServiceController::class, 'updateStatus'])
        ->name('services.update-status');
    Route::post('/services/{serviceOrder}/verify-payment', [Admin\ServiceController::class, 'verifyPayment'])
        ->name('services.verify-payment');
});

require __DIR__.'/auth.php';
