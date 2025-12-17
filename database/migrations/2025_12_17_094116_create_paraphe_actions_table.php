<?php
// database/migrations/2025_12_17_094116_create_paraphe_actions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paraphe_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parapheur_id')->constrained('parapheurs');
            $table->foreignId('user_id')->constrained('users');
            
            // Action métier
            $table->enum('action', [
                'creation',
                'analyse', 
                'transmission',
                'validation',
                'signature',
                'rejet',
                'observation',
                'archivage'
            ]);
            
            // Détails
            $table->text('details')->nullable();
            $table->string('statut_avant')->nullable();
            $table->string('statut_apres')->nullable();
            
            // Signature électronique (optionnel)
            $table->json('signature_data')->nullable();
            $table->string('signature_hash')->nullable();
            
            $table->timestamps();
            
            // Index pour recherche
            $table->index(['parapheur_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paraphe_actions');
    }
};