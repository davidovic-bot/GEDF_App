<?php
// database/migrations/2025_12_17_094119_create_observations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parapheur_id')->constrained('parapheurs');
            $table->foreignId('user_id')->constrained('users');
            
            // Type d'observation
            $table->enum('type', [
                'observation', 
                'recommandation', 
                'alerte',
                'question',
                'reponse'
            ])->default('observation');
            
            // Contenu
            $table->text('contenu');
            
            // Réponse à une observation parente
            $table->foreignId('parent_id')->nullable()->constrained('observations');
            $table->boolean('resolu')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};