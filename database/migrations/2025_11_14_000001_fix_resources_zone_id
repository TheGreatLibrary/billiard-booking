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
        // Если миграция 2025_11_11_000001 уже удалила zone_id, нужно его вернуть как nullable
        if (!Schema::hasColumn('resources', 'zone_id')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->foreignId('zone_id')->nullable()->after('place_id')
                    ->constrained('zones')->nullOnDelete();
                $table->index('zone_id');
            });
        } else {
            // Если zone_id существует, делаем его nullable и меняем constraint
            Schema::table('resources', function (Blueprint $table) {
                $table->foreignId('zone_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable(false)->change();
        });
    }
};