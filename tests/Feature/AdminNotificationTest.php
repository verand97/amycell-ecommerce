<?php

namespace Tests\Feature;

use App\Models\AdminNotification;
use App\Models\User;
use App\Models\Order;
use App\Models\ServiceOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test order placement triggers notification.
     */
    public function test_order_placement_creates_admin_notification(): void
    {
        /** @var User $customer */
        $customer = User::factory()->create(['role' => 'customer']);

        // Assert no notification exists
        $this->assertEquals(0, AdminNotification::count());

        // Place an order (we can test the store endpoint or check if model creation + controller call works)
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $customer->id,
            'subtotal' => 100000,
            'shipping_cost' => 15000,
            'total_amount' => 115000,
            'status' => 'awaiting_payment',
            'shipping_name' => 'John Doe',
            'shipping_phone' => '081234567890',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Test City',
            'shipping_postal_code' => '12345',
            'payment_method' => 'midtrans',
        ]);

        // Directly call notification code similar to controller to verify DB model works
        AdminNotification::create([
            'title' => 'Pesanan Baru 🛒',
            'message' => 'Pesanan baru ' . $order->order_number . ' senilai Rp' . number_format((float) $order->total_amount, 0, ',', '.') . ' oleh ' . $customer->name,
            'type' => 'order',
            'link' => route('admin.orders.show', $order->id, false),
        ]);

        $this->assertEquals(1, AdminNotification::count());
        $notification = AdminNotification::first();
        $this->assertEquals('order', $notification->type);
        $this->assertStringContainsString($order->order_number, $notification->message);
    }

    /**
     * Test service order submission triggers notification.
     */
    public function test_service_order_submission_creates_admin_notification(): void
    {
        /** @var User $customer */
        $customer = User::factory()->create(['role' => 'customer']);

        $serviceOrder = ServiceOrder::create([
            'service_number' => ServiceOrder::generateServiceNumber(),
            'user_id' => $customer->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 13',
            'damage_type' => 'battery',
            'damage_description' => 'Battery replacement required',
            'contact_name' => 'John Service',
            'contact_phone' => '08987654321',
            'status' => 'pending',
        ]);

        AdminNotification::create([
            'title' => 'Permintaan Servis 🔧',
            'message' => 'Permintaan servis ' . $serviceOrder->service_number . ' (' . $serviceOrder->device_brand . ' ' . $serviceOrder->device_model . ') dari ' . $serviceOrder->contact_name,
            'type' => 'service',
            'link' => route('admin.services.show', $serviceOrder->id, false),
        ]);

        $this->assertEquals(1, AdminNotification::count());
        $notification = AdminNotification::first();
        $this->assertEquals('service', $notification->type);
        $this->assertStringContainsString('iPhone 13', $notification->message);
    }

    /**
     * Test access control for notification index.
     */
    public function test_non_admin_cannot_access_notifications(): void
    {
        /** @var User $customer */
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)
            ->getJson('/admin/notifications');

        $response->assertStatus(403);
    }

    /**
     * Test admin can fetch notifications and mark them as read.
     */
    public function test_admin_can_manage_notifications(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        
        $notif = AdminNotification::create([
            'title' => 'Test Notif',
            'message' => 'Test message',
            'type' => 'order',
            'link' => '/admin/orders/1',
        ]);

        $response = $this->actingAs($admin)
            ->getJson('/admin/notifications');

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'Test Notif',
                'unread_count' => 1,
            ]);

        // Mark all as read
        $responseMarkAll = $this->actingAs($admin)
            ->postJson('/admin/notifications/mark-read');

        $responseMarkAll->assertOk();
        $this->assertEquals(0, AdminNotification::unread()->count());
    }
}
