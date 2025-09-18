<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('place_id')->constrained('places')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('total_amount')->default(0); // руб*100
            $table->index('user_id');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            $table->enum('type', ['table_time','equipment']);

            $table->foreignId('booking_resource_id')->nullable()
                ->constrained('booking_resources')->cascadeOnDelete(); // для table_time

            $table->foreignId('product_model_id')->nullable()
                ->constrained('product_models')->restrictOnDelete();  // для equipment

            $table->integer('qty')->default(1);
            $table->integer('price_each')->nullable(); // для equipment
            $table->integer('amount');                 // итог по строке (руб*100)

            $table->index('order_id');
            $table->index('booking_resource_id');
        });

        // 1) Ровно одна позиция table_time на каждый booking_resource в рамках заказа
        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_one_ttable_per_br
BEFORE INSERT ON order_items
WHEN NEW.type='table_time'
BEGIN
  SELECT CASE
    WHEN NEW.booking_resource_id IS NULL
      THEN RAISE(ABORT,'TABLE_TIME_NEEDS_BOOKING_RESOURCE')
    WHEN EXISTS (
      SELECT 1 FROM order_items oi
      WHERE oi.order_id = NEW.order_id
        AND oi.type='table_time'
        AND oi.booking_resource_id = NEW.booking_resource_id
    ) THEN RAISE(ABORT,'DUP_TABLE_TIME_FOR_RESOURCE')
  END;
END;
SQL);

        // 2) Ограничения формы для equipment
        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_shape_equipment
BEFORE INSERT ON order_items
WHEN NEW.type='equipment'
BEGIN
  SELECT CASE
    WHEN NEW.product_model_id IS NULL
      THEN RAISE(ABORT,'EQUIPMENT_NEEDS_PRODUCT_MODEL')
    WHEN NEW.qty < 1
      THEN RAISE(ABORT,'EQUIPMENT_QTY_MIN_1')
  END;
END;
SQL);
    }

    public function down(): void {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_one_ttable_per_br;');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_shape_equipment;');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
