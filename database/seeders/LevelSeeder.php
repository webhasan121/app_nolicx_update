<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\LevelHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Level::truncate();
        LevelHistory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $levels = [
            [
                'req_users' => 0, 'vip_users' => 0, 'bonus' => 0,
                'rewards' => null
            ],[
                'req_users' => 980, 'vip_users' => 20, 'bonus' => 2,
                'rewards' => 'Buffet Lunch + Program'
            ],[
                'req_users' => 1950, 'vip_users' => 50, 'bonus' => 2,
                'rewards' => 'Business Tour (Cox\'s Bazar)'
            ],[
                'req_users' => 4900, 'vip_users' => 100, 'bonus' => 3,
                'rewards' => 'Android Mobile Phone'
            ],[
                'req_users' => 14500, 'vip_users' => 500, 'bonus' => 4,
                'rewards' => 'Foreign (Maldives) Tour'
            ],[
                'req_users' => 29000, 'vip_users' => 1000, 'bonus' => 4,
                'rewards' => 'Motor Cycle'
            ],[
                'req_users' => 65000, 'vip_users' => 5000, 'bonus' => 5,
                'rewards' => 'Car'
            ],[
                'req_users' => 140000, 'vip_users' => 10000, 'bonus' => 5,
                'rewards' => 'Apartment (Flat)'
            ],
            // [
            //     'req_users' => 0, 'vip_users' => 0, 'bonus' => 0,
            //     'rewards' => ''
            // ],
        ];

        foreach ($levels as $key => $level) {
            Level::create([
                'name' => 'Star-' . $key,
                'req_users' => $level['req_users'],
                'vip_users' => $level['vip_users'],
                'bonus' => $level['bonus'],
                'rewards' => $level['rewards'] ?? null,
                'status' => true,
            ]);
        }
    }
}
