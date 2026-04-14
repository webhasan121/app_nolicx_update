<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // roles list
        $roles = [
            'system',
            'admin',
            'vendor',
            'reseller',
            'rider',
            'user'
        ];

        // loop through the roles list

        foreach ($roles as $role) {
            // create a new role
            Role::create([
                'name' => $role
            ]);
        };
    }
}
