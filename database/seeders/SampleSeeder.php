<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'carts',
            'cart_orders',
            'cod',
            'developer_accesses',
            'distribute_comissions',
            'management_accesses',
            'orders',
            'reseller_resell_profits',
            'stores',
            'store_logs',
            'sync_orders',
            'take_comissions',
            'withdraws'
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            LevelSeeder::class
        ]);
    }
}
