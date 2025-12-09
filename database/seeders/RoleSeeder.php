<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'superadmin'],
            ['name' => 'admin'],
            ['name' => 'secretaire'],
            ['name' => 'gestionnaire'],
            ['name' => 'chef_service'],
            ['name' => 'directeur'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}