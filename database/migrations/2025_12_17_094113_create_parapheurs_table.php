<?php
// database/migrations/2025_12_17_094113_create_parapheurs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parapheurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courrier_id')->constrained('courriers')->unique();
            $table->string('numero_parapheur')->unique();
            
            // Statuts métier
            $table->enum('statut_actuel', [
                'en_attente_analyse',
                'en_attente_chef_service', 
                'en_attente_directeur',
                'valide',
                'signe',
                'rejete',
                'archive'
            ])->default('en_attente_analyse');
            
            $table->enum('priorite', ['normal', 'urgent'])->default('normal');
            
            // Dates
            $table->dateTime('date_entree_parapheur');
            $table->date('date_limite_traitement')->nullable();
            $table->date('date_signature')->nullable();
            
            // Acteurs
            $table->string('derniere_action')->nullable();
            $table->foreignId('dernier_acteur_id')->nullable()->constrained('users');
            $table->foreignId('current_user_id')->nullable()->constrained('users'); // Actuel responsable
            
            // Rejet
            $table->text('motif_rejet')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parapheurs');
    }
};