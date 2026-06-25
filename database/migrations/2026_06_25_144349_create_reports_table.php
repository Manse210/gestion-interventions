<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->text('travaux');
            $table->integer('duree_heures')->default(0);
            $table->integer('duree_minutes')->default(0);
            $table->text('materiel')->nullable();
            $table->text('observations')->nullable();
            $table->text('recommandations')->nullable();
            $table->string('signed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
