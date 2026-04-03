<?php

namespace Database\Seeders;

use App\Models\Package_pays;
use App\Models\Packages;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Packages::insert(
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 2000,
                'countdown' => 5,
                'status' => 1,
                'coin' => 20,
                'm_coin' => 550,
                'ref_owner_get_coin' => 100,
            ],
            [
                'name' => 'Intermediate',
                'slug' => 'intermediate',
                'price' => 3500,
                'countdown' => 4,
                'status' => 1,
                'coin' => 50,
                'm_coin' => 2400,
                'ref_owner_get_coin' => 300,
            ],
        );

        Package_pays::create(
            [
                'package_id' => Packages::latest()->get()->id,
                'pay_type' => 'Bkash',
                'pay_to' => '01987654321'
            ]
        );
    }
}
