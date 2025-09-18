<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->restrictOnDelete();

            $table->string('starts_at'); // можно отличаться от общей booking.*
            $table->string('ends_at');

            $table->integer('hour_price_snapshot');   // руб*100
            $table->enum('rule_kind', ['coef','override']);
            $table->decimal('rule_value', 10, 3);     // coef или override цена/час (см. твои правила)
            $table->decimal('zone_coef_snapshot', 6, 3);
            $table->integer('minutes');
            $table->integer('amount');                // ИТОГО за этот стол (руб*100)

            $table->unique(['booking_id','resource_id']);
            $table->index(['resource_id','starts_at','ends_at']);
        });

        // Триггеры для SQLite: запрет пересечения по одному столу
        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_br_no_overlap
BEFORE INSERT ON booking_resources
BEGIN
  SELECT CASE
    WHEN EXISTS (
      SELECT 1 FROM booking_resources br
      WHERE br.resource_id = NEW.resource_id
        AND NOT (NEW.ends_at <= br.starts_at OR NEW.starts_at >= br.ends_at)
        AND br.booking_id <> NEW.booking_id
    ) THEN RAISE(ABORT,'OVERLAP')
  END;
END;
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_br_no_overlap_upd
BEFORE UPDATE OF resource_id, starts_at, ends_at ON booking_resources
BEGIN
  SELECT CASE
    WHEN EXISTS (
      SELECT 1 FROM booking_resources br
      WHERE br.id <> NEW.id
        AND br.resource_id = NEW.resource_id
        AND NOT (NEW.ends_at <= br.starts_at OR NEW.starts_at >= br.ends_at)
    ) THEN RAISE(ABORT,'OVERLAP')
  END;
END;
SQL);
    }

    public function down(): void {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_br_no_overlap;');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_br_no_overlap_upd;');
        Schema::dropIfExists('booking_resources');
    }
};
