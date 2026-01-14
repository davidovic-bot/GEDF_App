<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ex: DRS, DGF, etc.
            $table->string('nom');
            $table->string('sigle')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('responsable_nom')->nullable(); // Nom du chef de service
            $table->string('responsable_email')->nullable();
            $table->string('responsable_telephone')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->integer('ordre_affichage')->default(0);
            $table->timestamps();
            
            // Index
            $table->index('code');
            $table->index('est_actif');
            $table->index('ordre_affichage');
        });
        
        // Insérer les services de base de la DGI
        $this->insererServicesParDefaut();
    }
    
    private function insererServicesParDefaut(): void
    {
        $services = [
            [
                'code' => 'DRS',
                'nom' => 'Direction des Régimes Spécifiques',
                'sigle' => 'DRS',
                'description' => 'Gestion des régimes fiscaux spécifiques et des demandes d\'exonération',
                'ordre_affichage' => 1
            ],
            [
                'code' => 'DGF',
                'nom' => 'Direction des Grandes Entreprises',
                'sigle' => 'DGF',
                'description' => 'Gestion fiscale des grandes entreprises',
                'ordre_affichage' => 2
            ],
            [
                'code' => 'DVF',
                'nom' => 'Direction des Vérifications Fiscales',
                'sigle' => 'DVF',
                'description' => 'Contrôle et vérification des déclarations fiscales',
                'ordre_affichage' => 3
            ],
            [
                'code' => 'DLF',
                'nom' => 'Direction de la Législation Fiscale',
                'sigle' => 'DLF',
                'description' => 'Élaboration et interprétation des textes fiscaux',
                'ordre_affichage' => 4
            ],
            [
                'code' => 'DCF',
                'nom' => 'Direction du Contentieux Fiscale',
                'sigle' => 'DCF',
                'description' => 'Gestion des litiges et contentieux fiscaux',
                'ordre_affichage' => 5
            ],
            [
                'code' => 'DRH',
                'nom' => 'Direction des Ressources Humaines',
                'sigle' => 'DRH',
                'description' => 'Gestion du personnel de la DGI',
                'ordre_affichage' => 6
            ],
            [
                'code' => 'DI',
                'nom' => 'Direction de l\'Informatique',
                'sigle' => 'DI',
                'description' => 'Gestion des systèmes d\'information et infrastructures IT',
                'ordre_affichage' => 7
            ]
        ];
        
        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};