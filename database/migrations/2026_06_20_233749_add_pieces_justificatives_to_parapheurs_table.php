<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('parapheurs', function (Blueprint $table) {
            // Pièces justificatives
            if (!Schema::hasColumn('parapheurs', 'tableau_factures')) {
                $table->string('tableau_factures')->nullable()->after('priorite');
            }
            
            if (!Schema::hasColumn('parapheurs', 'factures')) {
                $table->json('factures')->nullable()->after('tableau_factures');
            }
            
            // Montants
            if (!Schema::hasColumn('parapheurs', 'montant_tva')) {
                $table->decimal('montant_tva', 15, 2)->nullable()->after('factures');
            }
            
            if (!Schema::hasColumn('parapheurs', 'montant_css')) {
                $table->decimal('montant_css', 15, 2)->nullable()->after('montant_tva');
            }
            
            if (!Schema::hasColumn('parapheurs', 'montant_total')) {
                $table->decimal('montant_total', 15, 2)->nullable()->after('montant_css');
            }
            
            // Vérification (Agent)
            if (!Schema::hasColumn('parapheurs', 'verifie_par')) {
                $table->foreignId('verifie_par')->nullable()->constrained('users')->after('montant_total');
            }
            
            if (!Schema::hasColumn('parapheurs', 'verifie_le')) {
                $table->timestamp('verifie_le')->nullable()->after('verifie_par');
            }
            
            // Contrôle (Chef)
            if (!Schema::hasColumn('parapheurs', 'controle_par')) {
                $table->foreignId('controle_par')->nullable()->constrained('users')->after('verifie_le');
            }
            
            if (!Schema::hasColumn('parapheurs', 'controle_le')) {
                $table->timestamp('controle_le')->nullable()->after('controle_par');
            }
            
            // Visa final (Directeur)
            if (!Schema::hasColumn('parapheurs', 'visa_final_par')) {
                $table->foreignId('visa_final_par')->nullable()->constrained('users')->after('controle_le');
            }
            
            if (!Schema::hasColumn('parapheurs', 'visa_final_le')) {
                $table->timestamp('visa_final_le')->nullable()->after('visa_final_par');
            }
            
            // ⚠️ motif_rejet : on ne fait RIEN car elle existe déjà
        });
    }

    public function down()
    {
        Schema::table('parapheurs', function (Blueprint $table) {
            // Supprimer les clés étrangères si elles existent
            if (Schema::hasColumn('parapheurs', 'verifie_par')) {
                $table->dropForeign(['verifie_par']);
            }
            if (Schema::hasColumn('parapheurs', 'controle_par')) {
                $table->dropForeign(['controle_par']);
            }
            if (Schema::hasColumn('parapheurs', 'visa_final_par')) {
                $table->dropForeign(['visa_final_par']);
            }
            
            // Supprimer les colonnes uniquement si elles existent
            $columns = ['tableau_factures', 'factures', 'montant_tva', 'montant_css', 'montant_total', 'verifie_par', 'verifie_le', 'controle_par', 'controle_le', 'visa_final_par', 'visa_final_le'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('parapheurs', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Ne pas supprimer motif_rejet car elle existait déjà avant
        });
    }
};