<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_with_valid_signature_updates_order_status()
    {
        // Fake event broadcasting
        Event::fake();

        // Create a user and an order
        $user = User::factory()->create();
        $order = Order::create([
            'order_number' => 'AMY202606020001',
            'user_id' => $user->id,
            'subtotal' => 50000,
            'shipping_cost' => 15000,
            'total_amount' => 65000,
            'status' => 'awaiting_payment',
            'shipping_name' => 'John Doe',
            'shipping_phone' => '08123456789',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Jakarta',
            'shipping_postal_code' => '12345',
            'payment_method' => 'midtrans',
        ]);

        $serverKey = config('midtrans.server_key');
        $signatureKey = hash("sha512", $order->order_number . '200' . '65000' . $serverKey);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => '65000',
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-tx-12345',
            'va_numbers' => [
                [
                    'bank' => 'bca',
                    'va_number' => '1234567890'
                ]
            ]
        ];

        $response = $this->postJson(route('midtrans.webhook'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Success']);

        // Check order updated to paid
        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertNotNull($order->paid_at);

        // Check transaction created and verified
        $transaction = Transaction::where('order_id', $order->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('verified', $transaction->status);
        $this->assertEquals('BCA', $transaction->bank_name);
        $this->assertEquals('midtrans-tx-12345', $transaction->transaction_code);
    }

    public function test_webhook_with_invalid_signature_returns_forbidden()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'order_number' => 'AMY202606020002',
            'user_id' => $user->id,
            'subtotal' => 50000,
            'shipping_cost' => 15000,
            'total_amount' => 65000,
            'status' => 'awaiting_payment',
            'shipping_name' => 'John Doe',
            'shipping_phone' => '08123456789',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Jakarta',
            'shipping_postal_code' => '12345',
            'payment_method' => 'midtrans',
        ]);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => '65000',
            'signature_key' => 'invalid-signature-key-value',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ];

        $response = $this->postJson(route('midtrans.webhook'), $payload);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Invalid signature']);
    }
}
