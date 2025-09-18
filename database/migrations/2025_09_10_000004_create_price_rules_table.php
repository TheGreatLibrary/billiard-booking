<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->cascadeOnDelete(); // NULL = для всех зон места
            $table->tinyInteger('dow')->nullable(); // 0..6
            $table->string('time_from', 5)->nullable(); // 'HH:MM'
            $table->string('time_to', 5)->nullable();
            $table->enum('kind', ['coef','override']);
            $table->decimal('value', 10, 3); // coef: множитель; override: руб*100 (целое, но оставим decimal)
            $table->boolean('active')->default(true);

            $table->index(['place_id','zone_id','dow']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('price_rules');
    }
};