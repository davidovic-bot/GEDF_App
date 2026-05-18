<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE courriers MODIFY statut_general ENUM('brouillon', 'enregistre', 'en_analyse', 'en_parapheur', 'traite', 'archive') NOT NULL DEFAULT 'enregistre'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE courriers MODIFY statut_general ENUM('brouillon', 'enregistre', 'en_parapheur', 'traite', 'archive') NOT NULL DEFAULT 'enregistre'");
    }
};