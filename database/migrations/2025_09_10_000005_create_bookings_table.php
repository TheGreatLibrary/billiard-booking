<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('place_id')->constrained('places')->restrictOnDelete();
            $table->string('starts_at'); // ISO8601 текстом для SQLite
            $table->string('ends_at');
            $table->enum('status', ['pending','confirmed','canceled','finished','no_show'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->text('comment')->nullable();

            $table->index('user_id');
            $table->index(['place_id','starts_at','ends_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};
