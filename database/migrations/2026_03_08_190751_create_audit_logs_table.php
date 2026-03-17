<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('module');
            $table->text('description')->nullable();
            $table->string('adresse_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('anciennes_valeurs')->nullable();
            $table->json('nouvelles_valeurs')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['module', 'created_at']);
            $table->index(['utilisateur_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};