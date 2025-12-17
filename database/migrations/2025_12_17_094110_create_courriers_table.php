<?php
// database/migrations/2025_12_17_094110_create_courriers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courriers', function (Blueprint $table) {
            $table->id();
            
            // Identification
            $table->string('numero')->unique();
            $table->unsignedBigInteger('type_courrier_id')->nullable();
            $table->string('objet');
            $table->string('reference')->nullable();
            
            // Origine
            $table->foreignId('service_emetteur_id')->nullable()->constrained('services');
            $table->foreignId('direction_id')->nullable()->constrained('directions');
            
            // Dates importantes
            $table->date('date_reception');
            $table->date('date_envoi')->nullable();
            $table->date('date_traitement')->nullable();
            
            // Contenu financier
            $table->decimal('montant', 15, 2)->nullable();
            $table->string('devise')->default('XAF');
            $table->string('beneficiaire')->nullable();
            $table->text('motif')->nullable();
            
            // Classification
            $table->enum('urgence', ['normal', 'moyenne', 'haute'])->default('normal');
            $table->enum('confidentialite', ['standard', 'confidentiel', 'secret'])->default('standard');
            
            // Statut
            $table->enum('statut_general', ['brouillon', 'enregistre', 'en_parapheur', 'traite', 'archive'])->default('enregistre');
            
            // Métadonnées
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courriers');
    }
};