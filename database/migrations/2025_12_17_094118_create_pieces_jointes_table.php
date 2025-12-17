<?php
// database/migrations/2025_12_17_094118_create_pieces_jointes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courrier_id')->constrained('courriers');
            
            // Fichier
            $table->string('nom_original');
            $table->string('nom_stockage')->unique();
            $table->string('chemin');
            $table->string('extension');
            $table->string('type_mime');
            $table->integer('taille')->default(0); // en octets
            
            // Métadonnées
            $table->string('description')->nullable();
            $table->enum('categorie', [
                'document', 
                'justificatif', 
                'signature',
                'autre'
            ])->default('document');
            
            // Sécurité
            $table->string('hash')->nullable(); // Pour vérifier l'intégrité
            $table->boolean('verifie')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
    }
};