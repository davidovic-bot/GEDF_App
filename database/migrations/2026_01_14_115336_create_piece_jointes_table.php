<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piece_jointes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courrier_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->string('mime_type');
            $table->unsignedInteger('taille'); // en octets
            $table->text('description')->nullable();
            $table->string('categorie')->nullable(); // justificatif, contrat, autre
            $table->boolean('est_obligatoire')->default(false);
            $table->timestamps();
            
            // Index
            $table->index('courrier_id');
            $table->index('user_id');
            $table->index('categorie');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piece_jointes');
    }
};