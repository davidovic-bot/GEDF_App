<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modifier la colonne type_attestation pour accepter les nouvelles valeurs
        DB::statement("ALTER TABLE parapheurs MODIFY COLUMN type_attestation ENUM('exoneration', 'dispense', 'exoneration_tva', 'dispense_tva', 'exoneration_css', 'dispense_css') NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE parapheurs MODIFY COLUMN type_attestation ENUM('exoneration', 'dispense') NULL");
    }
};