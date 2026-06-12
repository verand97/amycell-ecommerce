<?php

namespace Tests\Feature;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServicePaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can update service order payment status and method.
     */
    public function test_admin_can_update_service_payment_info()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $serviceOrder = ServiceOrder::create([
            'service_number' => ServiceOrder::generateServiceNumber(),
            'user_id' => User::factory()->create()->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 13',
            'damage_type' => 'battery',
            'damage_description' => 'Replace battery',
            'contact_name' => 'John Service',
            'contact_phone' => '08987654321',
            'status' => 'completed',
            'final_cost' => 300000,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.services.update-status', $serviceOrder->id), [
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'final_cost' => 300000,
            ]);

        $response->assertStatus(302);
        $serviceOrder->refresh();
        $this->assertEquals('paid', $serviceOrder->payment_status);
        $this->assertEquals('cash', $serviceOrder->payment_method);
        $this->assertNotNull($serviceOrder->paid_at);
    }

    /**
     * Test customer can select cash payment.
     */
    public function test_customer_can_select_cash_payment()
    {
        /** @var User $customer */
        $customer = User::factory()->create(['role' => 'customer']);

        $serviceOrder = ServiceOrder::create([
            'service_number' => ServiceOrder::generateServiceNumber(),
            'user_id' => $customer->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 13',
            'damage_type' => 'battery',
            'damage_description' => 'Replace battery',
            'contact_name' => 'John Service',
            'contact_phone' => '08987654321',
            'status' => 'completed',
            'final_cost' => 300000,
        ]);

        $response = $this->actingAs($customer)
            ->post(route('customer.service.pay-cash', $serviceOrder->id));

        $response->assertStatus(302);
        $serviceOrder->refresh();
        $this->assertEquals('cash', $serviceOrder->payment_method);
        $this->assertEquals('unpaid', $serviceOrder->payment_status);
    }

    /**
     * Test customer can upload manual transfer proof.
     */
    public function test_customer_can_upload_transfer_proof()
    {
        Storage::fake('public');

        /** @var User $customer */
        $customer = User::factory()->create(['role' => 'customer']);

        $serviceOrder = ServiceOrder::create([
            'service_number' => ServiceOrder::generateServiceNumber(),
            'user_id' => $customer->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 13',
            'damage_type' => 'battery',
            'damage_description' => 'Replace battery',
            'contact_name' => 'John Service',
            'contact_phone' => '08987654321',
            'status' => 'completed',
            'final_cost' => 300000,
        ]);

        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($customer)
            ->post(route('customer.service.pay-transfer', $serviceOrder->id), [
                'payment_proof' => $file,
            ]);

        $response->assertStatus(302);
        $serviceOrder->refresh();
        $this->assertEquals('transfer', $serviceOrder->payment_method);
        $this->assertEquals('pending', $serviceOrder->payment_status);
        $this->assertNotNull($serviceOrder->payment_proof);
        $this->assertTrue(Storage::disk('public')->exists($serviceOrder->payment_proof));
    }

    /**
     * Test admin can verify manual transfer payment.
     */
    public function test_admin_can_verify_manual_payment()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $serviceOrder = ServiceOrder::create([
            'service_number' => ServiceOrder::generateServiceNumber(),
            'user_id' => User::factory()->create()->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 13',
            'damage_type' => 'battery',
            'damage_description' => 'Replace battery',
            'contact_name' => 'John Service',
            'contact_phone' => '08987654321',
            'status' => 'completed',
            'final_cost' => 300000,
            'payment_status' => 'pending',
            'payment_method' => 'transfer',
            'payment_proof' => 'proof.jpg',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.services.verify-payment', $serviceOrder->id));

        $response->assertStatus(302);
        $serviceOrder->refresh();
        $this->assertEquals('paid', $serviceOrder->payment_status);
        $this->assertNotNull($serviceOrder->paid_at);
    }

    /**
     * Test customer can generate Midtrans Snap token.
     */
    public function test_customer_can_generate_midtrans_token()
    {
        // Mock Midtrans API call
        $mockSnapToken = 'snap-token-xyz-123';
        
        $this->mock('alias:Midtrans\Snap', function ($mock) use ($mockSnapToken) {
            $mock->shouldReceive('getSnapToken')->andReturn($mockSnapToken);
        });

        /** @var User $customer */
        $customer = User::factory()->create(['role' => 'customer']);

        $serviceOrder = ServiceOrder::create([
            'service_number' => ServiceOrder::generateServiceNumber(),
            'user_id' => $customer->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 13',
            'damage_type' => 'battery',
            'damage_description' => 'Replace battery',
            'contact_name' => 'John Service',
            'contact_phone' => '08987654321',
            'status' => 'completed',
            'final_cost' => 300000,
        ]);

        $response = $this->actingAs($customer)
            ->postJson(route('customer.service.pay-midtrans', $serviceOrder->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'snap_token' => $mockSnapToken,
        ]);

        $serviceOrder->refresh();
        $this->assertEquals('midtrans', $serviceOrder->payment_method);
        $this->assertEquals($mockSnapToken, $serviceOrder->snap_token);
    }

    /**
     * Test webhook updates service order status.
     */
    public function test_webhook_updates_service_order()
    {
        $serviceOrder = ServiceOrder::create([
            'service_number' => 'SRV-20260612-TEST',
            'user_id' => User::factory()->create()->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 13',
            'damage_type' => 'battery',
            'damage_description' => 'Replace battery',
            'contact_name' => 'John Service',
            'contact_phone' => '08987654321',
            'status' => 'completed',
            'final_cost' => 300000,
            'payment_method' => 'midtrans',
        ]);

        $serverKey = config('midtrans.server_key');
        $signatureKey = hash("sha512", $serviceOrder->service_number . '200' . '300000' . $serverKey);

        $payload = [
            'order_id' => $serviceOrder->service_number,
            'status_code' => '200',
            'gross_amount' => '300000',
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-srv-123',
        ];

        $response = $this->postJson(route('midtrans.webhook'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Success']);

        $serviceOrder->refresh();
        $this->assertEquals('paid', $serviceOrder->payment_status);
        $this->assertNotNull($serviceOrder->paid_at);
        $this->assertEquals('midtrans', $serviceOrder->payment_method);
    }
}
