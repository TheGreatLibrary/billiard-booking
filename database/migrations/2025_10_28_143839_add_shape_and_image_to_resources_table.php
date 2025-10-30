<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('shape')->default('rect'); // rect | circle | ellipse | image
            $table->string('image_url')->nullable();  // для типа image
        });
    }

    public function down(): void {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['shape', 'image_url']);
        });
    }
};
