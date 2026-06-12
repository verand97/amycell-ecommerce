<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid'); // unpaid, pending, paid
            $table->string('payment_method')->nullable(); // cash, transfer, midtrans
            $table->string('snap_token')->nullable();
            $table->string('payment_proof')->nullable();
            $table->timestamp('paid_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_method', 'snap_token', 'payment_proof', 'paid_at']);
        });
    }
};
