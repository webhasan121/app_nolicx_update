<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::insert(
            [
                'name' => 'comission_store',
                'coin' => 0,
            ],
            [
                'name' => 'comission_take',
                'coin' => 0,
            ],
            [
                'name' => 'comission_give',
                'coin' => 0,
            ],
            [
                'name' => 'withdraw_server_cost',
                'coin' => 0,
            ],
            [
                'name' => 'withdraw_domain_cost',
                'coin' => 0,
            ],
        );
    }
}
