<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\user_has_refs;

class SystemRefsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $system = User::where('email', config('app.system_email'))->first();
        if ($system && !empty($system->myRef?->ref) && $system->myRef->ref != config('app.ref')) {

            /**
             * system has it's own reffer code 
             */
            $system->myRef()->update(
                [
                    'ref' => config('app.ref'),
                ]
            );

            // $system->syncRole($systemRole);
        } else {
            user_has_refs::create(
                [
                    'user_id' => $system->id,
                    'ref' => config('app.ref'),
                    'status' => 1,
                ]
            );
        }
    }
}
