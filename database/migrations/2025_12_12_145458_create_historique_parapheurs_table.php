<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historique_parapheurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parapheur_id')->constrained('parapheurs')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('action', [
                'creation',
                'modification',
                'validation',
                'rejet',
                'transmission',
                'ajout_fichier',
                'suppression_fichier',
                'changement_statut',
                'changement_priorite',
                'ajout_note',
                'relance',
                'archivage'
            ]);
            $table->text('details');
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['parapheur_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('historique_parapheurs');
    }
};