<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('resources', function (Blueprint $table) {
            $table->float('x')->default(0);
            $table->float('y')->default(0);
            $table->float('width')->default(100);
            $table->float('height')->default(100);
            $table->float('rotation')->default(0);
        });
    }

    public function down(): void {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['x', 'y', 'width', 'height', 'rotation']);
        });
    }
};
