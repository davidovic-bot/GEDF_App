<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fichier_parapheurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parapheur_id')->constrained('parapheurs')->onDelete('cascade');
            $table->string('nom_original');
            $table->string('nom_stockage');
            $table->string('chemin');
            $table->string('type_mime');
            $table->integer('taille');
            $table->string('extension');
            $table->foreignId('uploader_id')->constrained('users');
            $table->text('commentaire')->nullable();
            $table->integer('telechargements')->default(0);
            $table->boolean('est_signature')->default(false);
            $table->boolean('est_principal')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fichier_parapheurs');
    }
};