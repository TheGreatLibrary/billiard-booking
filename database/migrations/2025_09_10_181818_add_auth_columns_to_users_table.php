<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('password')->nullable()->after('email_verified_at'); // можно NOT NULL, если уже используешь auth
            $table->rememberToken();
            $table->timestamp('updated_at')->nullable(); // чтобы factories не ругались
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'password', 'remember_token', 'updated_at']);
        });
    }
};