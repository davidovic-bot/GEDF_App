<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courrier_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role_validation'); // chef_service, directeur
            $table->string('statut')->default('en_attente'); // en_attente, valide, signe, rejete, annule
            $table->integer('ordre')->default(1); // Ordre dans le workflow
            $table->timestamp('date_validation')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['courrier_id', 'statut']);
            $table->index(['user_id', 'statut']);
            $table->index(['role_validation', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};