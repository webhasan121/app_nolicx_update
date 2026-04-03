<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\user_has_refs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $system = User::where('email', config('app.system_email'))->first();
        $systemRole = Role::where('name', 'system')->first();

        if ($system) {

            /**
             * if user have
             * give the system role to user
             * by spatie role-permision package
             */
            if (!$system->hasRole($systemRole)) {
                $system->assignRole($systemRole);
            }


            /**
             * system has it's own reffer code 
             */
            // user_has_refs::create(
            //     [
            //         'user_id' => $system->id,
            //         'ref' => config('app.ref'),
            //         'status' => 1
            //     ]
            // );

            // $system->syncRole($systemRole);
        }


        // $permissions = Permission::all();
        // $permissions->syncRole($systemRole);
    }
}
