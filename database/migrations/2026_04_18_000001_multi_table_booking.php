<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Добавляем resource_id в booking_slots
        Schema::table('booking_slots', function (Blueprint $table) {
            $table->unsignedBigInteger('resource_id')->nullable()->after('booking_id');
            $table->foreign('resource_id')->references('id')->on('resources')->nullOnDelete();
            $table->index('resource_id');
        });

        // 2. Заполняем resource_id в существующих слотах из bookings.resource_id
        DB::statement('
            UPDATE booking_slots 
            SET resource_id = (
                SELECT resource_id FROM bookings WHERE bookings.id = booking_slots.booking_id
            )
            WHERE resource_id IS NULL
        ');

        // 3. Заменяем уникальный индекс: (booking_id, slot_datetime) → (booking_id, resource_id, slot_datetime)
        //    Старый индекс не позволяет 2 стола в одном бронировании на одно время
        Schema::table('booking_slots', function (Blueprint $table) {
            $table->dropUnique(['booking_id', 'slot_datetime']);
            $table->unique(['booking_id', 'resource_id', 'slot_datetime']);
        });
    }

    public function down(): void
    {
        Schema::table('booking_slots', function (Blueprint $table) {
            $table->dropUnique(['booking_id', 'resource_id', 'slot_datetime']);
            $table->unique(['booking_id', 'slot_datetime']);

            $table->dropForeign(['resource_id']);
            $table->dropIndex(['resource_id']);
            $table->dropColumn('resource_id');
        });
    }
};
