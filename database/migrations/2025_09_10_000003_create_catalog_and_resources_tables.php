<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('product_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->constrained('product_types')->restrictOnDelete();
            $table->string('name');
            $table->integer('base_price_hour')->nullable(); // руб*100; для equipment можно NULL
            $table->integer('base_price_each')->nullable(); // для equipment
            $table->index('product_type_id');
        });

        Schema::create('state_product', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('product_models')->restrictOnDelete();
            $table->foreignId('zone_id')->constrained('zones')->restrictOnDelete();
            $table->string('code')->nullable(); // "A-05"
            $table->foreignId('state_id')->constrained('state_product')->restrictOnDelete();
            $table->text('note')->nullable();

            $table->index('zone_id');
            $table->index('model_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('resources');
        Schema::dropIfExists('state_product');
        Schema::dropIfExists('product_models');
        Schema::dropIfExists('product_types');
    }
};