<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ref')->unique();
            $table->string('titre');
            $table->text('description');
            $table->string('priorite'); // Critique, Haute, Moyenne, Basse
            $table->string('categorie'); // Réseau, Matériel, Logiciel, Électrique, Autre
            $table->string('statut')->default('Ouvert'); // Ouvert, En cours, Rapport en rédaction, Résolu, Fermé
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('technicien_id')->nullable()->constrained('users');
            $table->string('piece_jointe')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
