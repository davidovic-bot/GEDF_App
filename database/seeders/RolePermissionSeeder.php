<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Afficher les rôles existants
        $existingRoles = DB::table('roles')->pluck('name');
        $this->command->info('✅ Les rôles existent déjà.');
        $this->command->info('📋 Rôles existants: ' . $existingRoles->implode(', '));

        // 2. Générer des valeurs
        $matricule = 'ADMIN' . date('Ymd') . rand(100, 999);

        // 3. Vérifier l'existence de l'utilisateur
        $admin = User::where('email', 'admin@dgi.ga')->first();

        if (!$admin) {
            // Créer l'utilisateur
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@dgi.ga',
                'password' => Hash::make('password'),
                'matricule' => $matricule,
                'poste' => 'Super Administrateur',
                'actif' => 1,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✅ Nouvel admin créé avec succès !');
            $this->command->info('📧 Email: admin@dgi.ga');
            $this->command->info('🔑 Mot de passe: password');
        } else {
            $this->command->info('✅ L\'admin existe déjà.');
            $this->command->info('📧 Email: admin@dgi.ga');
        }

        // 4. Note sur la liaison role-user
        $this->command->warn('⚠️  Attention: La liaison user_roles n\'existe pas.');
        $this->command->warn('👉 Tu devras assigner manuellement le rôle à l\'utilisateur dans ton interface.');
        
        // Afficher les IDs pour référence
        $this->command->info('📋 ID Utilisateur: ' . $admin->id);
        
        $superRole = DB::table('roles')->where('name', 'superadmin')->first();
        if ($superRole) {
            $this->command->info('📋 ID Rôle superadmin: ' . $superRole->id);
        } else {
            $firstRole = DB::table('roles')->first();
            if ($firstRole) {
                $this->command->info('📋 ID Premier rôle: ' . $firstRole->id);
            }
        }
    }
}