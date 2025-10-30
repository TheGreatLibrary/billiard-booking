<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up()
{
    Schema::table('resources', function (Blueprint $table) {
$table->float('x')->nullable();
$table->float('y')->nullable();
$table->float('width')->default(100);
$table->float('height')->default(60);
$table->float('rotation')->default(0);
$table->string('shape')->default('rect');

    });
}
};
