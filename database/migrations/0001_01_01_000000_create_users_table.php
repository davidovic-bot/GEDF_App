<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('matricule')->unique();
                
                $table->unsignedBigInteger('service_id')->nullable();
                $table->unsignedBigInteger('direction_id')->nullable();
                
                $table->string('poste');
                $table->string('telephone')->nullable();
                $table->boolean('actif')->default(true);
                $table->timestamp('derniere_connexion')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};