<?php

use Illuminate\Support\Facades\Broadcast;

// Customer private channel for order status updates
Broadcast::channel('orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Chat session channel (both customer and admin with session access)
Broadcast::channel('chat.{sessionId}', function ($user, $sessionId) {
    $session = \App\Models\ChatSession::find($sessionId);
    if (!$session) return false;

    return $user->id === $session->customer_id
        || ($user->isAdmin() && $user->id === $session->admin_id)
        || $user->isAdmin(); // Admin can access any active session
});

// Admin broadcast channel
Broadcast::channel('admin.chat', function ($user) {
    return $user->isAdmin();
});
