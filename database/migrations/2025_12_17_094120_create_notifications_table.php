<?php
// database/migrations/2025_12_17_094120_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            
            // Contenu
            $table->string('type'); // nouveau_parapheur, validation, signature, retard, etc.
            $table->string('titre');
            $table->text('message');
            $table->json('data')->nullable(); // Données supplémentaires
            
            // Lien
            $table->string('lien')->nullable();
            $table->string('lien_texte')->nullable();
            
            // État
            $table->boolean('lu')->default(false);
            $table->timestamp('date_lecture')->nullable();
            
            // Expiration
            $table->timestamp('expire_at')->nullable();
            
            $table->timestamps();
            
            // Index pour performance
            $table->index(['user_id', 'lu', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};