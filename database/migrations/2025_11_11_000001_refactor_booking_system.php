<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Удаляем старые таблицы
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('booking_resources');

        // 2. Модифицируем places - добавляем размеры зала (сетка)
        Schema::table('places', function (Blueprint $table) {
            $table->integer('grid_width')->default(20); // клеток по горизонтали
            $table->integer('grid_height')->default(10); // клеток по вертикали
            $table->string('hall_image')->nullable(); // путь к картинке
        });

        // 3. Модифицируем zones - добавляем координаты полигона
        Schema::table('zones', function (Blueprint $table) {
            $table->json('coordinates')->nullable(); // [{x: 0, y: 0}, {x: 100, y: 0}, ...]
            $table->string('color')->default('#3B82F6'); // цвет зоны на карте
        });

        // 4. Модифицируем resources - координаты и визуализация
        Schema::table('resources', function (Blueprint $table) {
            // Убираем zone_id constraint, теперь ресурс привязан к place
            $table->dropForeign(['zone_id']);
            $table->dropIndex(['zone_id']);
            
            // Добавляем place_id
            $table->foreignId('place_id')->after('model_id')
                ->constrained('places')->cascadeOnDelete();
            
            // Координаты на сетке (null для невизуальных ресурсов типа кий)
            $table->integer('grid_x')->nullable()->after('note'); // позиция по X (0-19)
            $table->integer('grid_y')->nullable(); // позиция по Y (0-9)
            $table->integer('grid_width')->default(2); // ширина в клетках
            $table->integer('grid_height')->default(1); // высота в клетках
            $table->integer('rotation')->default(0); // 0, 90, 180, 270 градусов
            
            $table->index('place_id');
        });

        // 5. Создаем новую упрощенную таблицу booking_slots
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('slot_date'); // 2025-11-11
            $table->string('slot_time'); // 12:00, 13:00, 14:00...
            $table->timestamp('slot_datetime'); // полная дата-время для сортировки
            
            $table->index(['booking_id', 'slot_date']);
            $table->unique(['booking_id', 'slot_datetime']); // один слот = уникальное время
        });

        // 6. Модифицируем bookings - упрощаем
        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropIndex(['place_id', 'starts_at', 'ends_at']);
            });
        } catch (\Exception $e) {
            // Индекс может не существовать - игнорируем
        }

        // Шаг 6.2: Добавляем новые поля
        Schema::table('bookings', function (Blueprint $table) {
            // Payment fields
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'canceled'])
                ->default('pending')->after('status');
            $table->enum('payment_method', ['card', 'online'])->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
            
            // Единственный resource
            $table->unsignedBigInteger('resource_id')->nullable()->after('place_id');
            
            // Итоговая сумма
            $table->integer('total_amount')->default(0)->after('comment'); // руб*100
            
            // Guest данные (если без аккаунта)
            $table->string('guest_name')->nullable()->after('total_amount');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            
            // TTL для pending
            $table->timestamp('expires_at')->nullable()->after('created_at');
        });

        // Шаг 6.3: Удаляем starts_at и ends_at
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['starts_at', 'ends_at']);
        });

        // Шаг 6.4: Добавляем foreign key для resource_id
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('resource_id')->references('id')->on('resources')->nullOnDelete();
        });

        // 7. Создаем таблицу для доп. оборудования в бронировании
        Schema::create('booking_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('product_model_id')->constrained('product_models')->restrictOnDelete();
            $table->integer('qty')->default(1);
            $table->integer('price_each'); // руб*100 на момент бронирования
            $table->integer('amount'); // руб*100 итого
            
            $table->index('booking_id');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('booking_equipment');
        Schema::dropIfExists('booking_slots');
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status', 'payment_method', 'paid_at',
                'resource_id', 'total_amount',
                'guest_name', 'guest_email', 'guest_phone', 'expires_at'
            ]);
            $table->string('starts_at');
            $table->string('ends_at');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['place_id']);
            $table->dropIndex(['place_id']);
            $table->dropColumn(['place_id', 'grid_x', 'grid_y', 'grid_width', 'grid_height', 'rotation']);
            
            $table->foreignId('zone_id')->after('model_id')->constrained('zones')->cascadeOnDelete();
            $table->index('zone_id');
        });

        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn(['coordinates', 'color']);
        });

        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn(['grid_width', 'grid_height', 'hall_image']);
        });

        // Восстанавливаем старые таблицы (упрощенно)
        Schema::create('booking_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->restrictOnDelete();
            $table->string('starts_at');
            $table->string('ends_at');
            $table->integer('amount');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('place_id')->constrained('places')->restrictOnDelete();
            $table->integer('total_amount')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('type', ['table_time', 'equipment']);
            $table->integer('qty')->default(1);
            $table->integer('amount');
        });
    }
};
