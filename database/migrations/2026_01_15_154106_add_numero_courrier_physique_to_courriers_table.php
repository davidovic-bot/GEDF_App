<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            // Ajouter le numéro du courrier physique
            $table->string('numero_courrier_physique')->nullable()->after('reference');
            
            // Indiquer si c'est un courrier physique (toujours true pour l'instant)
            $table->boolean('est_courrier_physique')->default(true)->after('urgent');
            
            // Renommer le champ pour plus de clarté
            $table->renameColumn('created_by', 'enregistre_par');
            
            // Ajouter un index pour le numéro physique (recherche)
            $table->index('numero_courrier_physique');
            
            // Ajouter le champ pour le registre physique
            $table->string('registre_physique')->nullable()->after('numero_courrier_physique')
                  ->comment('Numéro dans le registre physique de courrier arrivé');
            
            // Ajouter le champ pour la date d'enregistrement physique
            $table->date('date_enregistrement_physique')->nullable()->after('date_reception')
                  ->comment('Date d\'enregistrement dans le registre physique');
            
            // Ajouter le champ pour l'assistante qui a reçu le courrier
            $table->foreignId('assistante_reception_id')->nullable()->after('enregistre_par')
                  ->constrained('users')->onDelete('set null')
                  ->comment('Assistante qui a physiquement reçu le courrier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            // Supprimer les nouveaux champs
            $table->dropColumn([
                'numero_courrier_physique',
                'est_courrier_physique',
                'registre_physique',
                'date_enregistrement_physique',
                'assistante_reception_id'
            ]);
            
            // Re-renommer le champ
            $table->renameColumn('enregistre_par', 'created_by');
            
            // Supprimer l'index
            $table->dropIndex(['numero_courrier_physique']);
        });
    }
};