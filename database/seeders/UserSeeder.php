<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'),
                'role_id' => 1, // superadmin
            ],
            [
                'name' => 'Admin DRS',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role_id' => 2, // admin
            ],
            [
                'name' => 'Secrétaire',
                'email' => 'secretaire@example.com',
                'password' => bcrypt('password'),
                'role_id' => 3, // secretaire
            ],
            [
                'name' => 'Gestionnaire 1',
                'email' => 'gestion1@example.com',
                'password' => bcrypt('password'),
                'role_id' => 4, // gestionnaire
            ],
            [
                'name' => 'Chef de Service',
                'email' => 'chefservice@example.com',
                'password' => bcrypt('password'),
                'role_id' => 5, // chef_service
            ],
            [
                'name' => 'Directeur DRS',
                'email' => 'directeur@example.com',
                'password' => bcrypt('password'),
                'role_id' => 6, // directeur
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}