<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            // Количество единиц этого ресурса
            // Для столов = 1 (уникальный ресурс с координатами)
            // Для инвентаря = N (общий пул доступных единиц)
            $table->integer('quantity')->default(1)->after('note');
            
            // Опционально: явное разделение типов
            // Можно и без этого поля, используя логику grid_x !== null
            $table->enum('type', ['table', 'equipment'])
                ->default('table')
                ->after('quantity');
        });
        
        // Установим правильные значения для существующих записей
        DB::statement("
            UPDATE resources 
            SET type = CASE 
                WHEN grid_x IS NOT NULL THEN 'table'
                ELSE 'equipment'
            END
        ");
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'type']);
        });
    }
};