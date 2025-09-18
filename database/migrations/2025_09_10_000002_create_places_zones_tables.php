<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->text('description')->nullable();
        });

        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price_coef', 6, 3)->default(1.0); // точнее, чем REAL
            $table->index('place_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('zones');
        Schema::dropIfExists('places');
    }
};