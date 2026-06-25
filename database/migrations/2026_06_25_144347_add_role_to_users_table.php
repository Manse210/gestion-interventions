<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('client')->after('password');
            $table->string('specialite')->nullable()->after('role');
            $table->boolean('actif')->default(true)->after('specialite');
            $table->string('avatar')->nullable()->after('actif');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'specialite', 'actif', 'avatar']);
        });
    }
};
