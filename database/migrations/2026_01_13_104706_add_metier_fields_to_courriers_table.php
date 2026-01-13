<?php
// database/migrations/xxxx_add_metier_fields_to_courriers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetierFieldsToCourriersTable extends Migration
{
    public function up()
    {
        Schema::table('courriers', function (Blueprint $table) {
            // Nouveaux champs métier
            $table->enum('type_dossier', [
                'exoneration', 
                'dispense_tva', 
                'rejet', 
                'autre'
            ])->default('exoneration')->after('description');
            
            $table->enum('statut', [
                'en_analyse',
                'en_validation', 
                'signe', 
                'archive'
            ])->default('en_analyse')->after('type_dossier');
            
            $table->date('date_limite')->nullable()->after('statut');
            $table->dateTime('date_decision')->nullable()->after('date_limite');
            $table->dateTime('date_archive')->nullable()->after('date_decision');
            
            // Informations contribuable
            $table->string('contribuable_nom')->nullable()->after('date_archive');
            $table->string('contribuable_id_fiscal')->nullable()->after('contribuable_nom');
            $table->string('secteur_activite')->nullable()->after('contribuable_id_fiscal');
            $table->decimal('montant_impact', 15, 2)->nullable()->after('secteur_activite');
            
            // Pour workflow
            $table->foreignId('service_id')->nullable()->after('createur_id');
            $table->text('motif_rejet')->nullable()->after('montant_impact');
            
            // Index pour performances
            $table->index(['type_dossier', 'statut']);
            $table->index('date_limite');
            $table->index('date_decision');
        });
    }
    
    public function down()
    {
        Schema::table('courriers', function (Blueprint $table) {
            $table->dropColumn([
                'type_dossier',
                'statut',
                'date_limite',
                'date_decision',
                'date_archive',
                'contribuable_nom',
                'contribuable_id_fiscal',
                'secteur_activite',
                'montant_impact',
                'service_id',
                'motif_rejet'
            ]);
            
            $table->dropIndex(['type_dossier', 'statut']);
            $table->dropIndex(['date_limite']);
            $table->dropIndex(['date_decision']);
        });
    }
}