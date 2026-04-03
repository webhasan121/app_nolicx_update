<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // get all permision and assign then into 'system' role
        $permissions = Permission::get('name');
        $systemRole = Role::where('name', 'system')->first();
        if (!$systemRole) {
            $systemRole = Role::create(['name' => 'system']);
        }

        // if permission is not assigned to system role, assign it
        if ($permissions) {
            foreach ($permissions as $permission) {
                if (!$systemRole->hasPermissionTo($permission->name)) {
                    $systemRole->givePermissionTo($permission->name);
                }
            }
        }

        // $system->syncPermissions($permissions);
    }
}
