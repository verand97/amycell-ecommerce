<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject')->nullable();
            $table->enum('status', ['waiting', 'active', 'closed'])->default('waiting');
            $table->integer('queue_position')->default(0);
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('unread_count_admin')->default(0);
            $table->integer('unread_count_customer')->default(0);
            $table->timestamps();

            // FIFO ordering index
            $table->index(['status', 'created_at']);
            $table->index('queue_position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
