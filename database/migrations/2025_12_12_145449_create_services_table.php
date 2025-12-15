<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
{
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->string('nom');
        
        // REMPLACE :
        // $table->foreignId('direction_id')->constrained('directions');
        // PAR :
        $table->unsignedBigInteger('direction_id')->nullable();
        
        $table->foreignId('chef_id')->nullable()->constrained('users');
        $table->text('description')->nullable();
        $table->string('email')->nullable();
        $table->string('telephone')->nullable();
        $table->boolean('actif')->default(true);
        $table->timestamps();
        $table->softDeletes();
    });
}

    public function down()
    {
        Schema::dropIfExists('services');
    }
};