<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les services
        $services = Service::all();
        
        if ($services->isEmpty()) {
            $this->call(ServiceSeeder::class);
            $services = Service::all();
        }
        
        // Créer un admin
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@dgi.ga',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'service_id' => $services->first()->id
        ]);
        
        // Créer un directeur
        User::create([
            'name' => 'Directeur DGI',
            'email' => 'directeur@dgi.ga',
            'password' => Hash::make('password'),
            'role' => 'directeur',
            'service_id' => $services->first()->id
        ]);
        
        // Créer des chefs de service
        foreach ($services as $service) {
            User::create([
                'name' => 'Chef ' . $service->sigle,
                'email' => 'chef.' . strtolower($service->code) . '@dgi.ga',
                'password' => Hash::make('password'),
                'role' => 'chef_service',
                'service_id' => $service->id
            ]);
            
            // Créer un agent
            User::create([
                'name' => 'Agent ' . $service->sigle,
                'email' => 'agent.' . strtolower($service->code) . '@dgi.ga',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'service_id' => $service->id
            ]);
            
            // Créer un secrétaire
            User::create([
                'name' => 'Secrétaire ' . $service->sigle,
                'email' => 'secretaire.' . strtolower($service->code) . '@dgi.ga',
                'password' => Hash::make('password'),
                'role' => 'secretaire',
                'service_id' => $service->id
            ]);
        }
    }
}