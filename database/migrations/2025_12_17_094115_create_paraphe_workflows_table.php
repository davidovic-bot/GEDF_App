<?php
// database/migrations/2025_12_17_094115_create_paraphe_workflows_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paraphe_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parapheur_id')->constrained('parapheurs')->unique();
            
            // Configuration du workflow
            $table->json('etapes_config')->nullable(); // [{ordre:1, role:'agent', delai:2}, ...]
            $table->integer('etape_actuelle')->default(1);
            
            // Dates
            $table->dateTime('date_debut');
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_fin_reelle')->nullable();
            
            // Suivi
            $table->integer('duree_estimee_jours')->default(5);
            $table->integer('duree_reelle_jours')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paraphe_workflows');
    }
};