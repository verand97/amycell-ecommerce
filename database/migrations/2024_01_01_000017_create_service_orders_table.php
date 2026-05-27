<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('service_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Device info
            $table->string('device_brand');
            $table->string('device_model');
            $table->string('device_color')->nullable();
            $table->string('device_imei')->nullable();

            // Damage info
            $table->string('damage_type');
            $table->text('damage_description');
            $table->string('device_image')->nullable();

            // Contact info
            $table->string('contact_name');
            $table->string('contact_phone', 20);
            $table->text('contact_address')->nullable();

            // Status & workflow
            $table->enum('status', [
                'pending',
                'received',
                'diagnosing',
                'waiting_approval',
                'repairing',
                'testing',
                'completed',
                'picked_up',
                'cancelled',
            ])->default('pending');

            // Cost
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('final_cost', 12, 2)->nullable();

            // Admin notes
            $table->text('admin_notes')->nullable();
            $table->text('diagnosis_notes')->nullable();
            $table->date('estimated_completion')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
