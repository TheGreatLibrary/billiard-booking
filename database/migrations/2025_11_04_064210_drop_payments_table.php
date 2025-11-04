<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payments');
    }

    public function down(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->integer('amount');
            $table->enum('method', ['cash','card','online']);
            $table->enum('status', ['pending','paid','refunded','failed']);
            $table->timestamp('paid_at')->nullable();
            $table->index(['order_id','status']);
        });
    }
};