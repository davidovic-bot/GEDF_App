<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('parapheurs', function (Blueprint $table) {
            $table->enum('type_attestation', ['exoneration', 'dispense'])
                  ->nullable()
                  ->after('objet')
                  ->comment('Exonération ou dispense ouverte (TVA, CSS, etc.)');
        });
    }

    public function down()
    {
        Schema::table('parapheurs', function (Blueprint $table) {
            $table->dropColumn('type_attestation');
        });
    }
};