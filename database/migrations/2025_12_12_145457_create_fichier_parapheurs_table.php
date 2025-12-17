<?php
// database/migrations/2025_12_12_145457_create_fichier_parapheurs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichier_parapheurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parapheur_id')->constrained('parapheurs');
            $table->string('nom_fichier');
            $table->string('chemin');
            $table->string('type');
            $table->integer('taille');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichier_parapheurs');
    }
};