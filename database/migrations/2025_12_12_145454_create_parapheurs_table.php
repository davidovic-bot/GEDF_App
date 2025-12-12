<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('parapheurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('objet');
            $table->text('description')->nullable();
            
            $table->enum('statut', [
                'brouillon', 
                'en_attente', 
                'en_cours', 
                'valide', 
                'rejete', 
                'en_retard',
                'archive'
            ])->default('brouillon');
            
            $table->enum('priorite', [
                'basse', 
                'normale', 
                'haute', 
                'urgente'
            ])->default('normale');
            
            $table->enum('confidentialite', [
                'standard',
                'confidentiel',
                'tres_confidentiel'
            ])->default('standard');
            
            $table->date('date_creation');
            $table->date('date_echeance');
            $table->dateTime('date_validation')->nullable();
            $table->dateTime('date_rejet')->nullable();
            
            $table->foreignId('createur_id')->constrained('users');
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('direction_id')->constrained('directions');
            $table->foreignId('responsable_actuel_id')->nullable()->constrained('users');
            
            $table->integer('etape_actuelle')->default(1);
            $table->integer('etapes_total')->default(3);
            $table->json('workflow')->nullable();
            
            $table->text('motif_rejet')->nullable();
            $table->text('notes_internes')->nullable();
            
            $table->boolean('notifier_createur')->default(true);
            $table->boolean('notifier_responsable')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['statut', 'date_echeance']);
            $table->index(['service_id', 'created_at']);
            $table->index(['direction_id', 'statut']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('parapheurs');
    }
};