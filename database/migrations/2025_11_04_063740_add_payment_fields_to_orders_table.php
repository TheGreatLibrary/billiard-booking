<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'canceled', 'refunded'])
                  ->default('pending')
                  ->after('total_amount');
            
            $table->enum('payment_method', ['cash', 'card', 'online'])
                  ->nullable()
                  ->after('status');
            
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_method', 'paid_at']);
        });
    }
};