<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        //
        DB::table('branches')->truncate();
        
        $branches = [
            [
                'name' => 'Head Office', 'email' => 'info@nolicx.com', 'address' => 'Sector-10, Uttara, Dhaka-1230'
            ],
            [
                'name' => 'Mirpur Branch', 'email' => 'mirpur@nolicx.com', 'address' => 'Section-12, Pallabi, Dhaka-1216'
            ],
        ];

        foreach ($branches as $key => $branch) {
            DB::table('branches')->insert([
                'name' => $branch['name'],
                'slug' => Str::slug($branch['name']),
                'email' => $branch['email'],
                'phone'   => $this->generateBdPhone(),
                'address' => $branch['address'],
                'type' =>$key === 0 ? 'Prime' : 'Other',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function generateBdPhone(): string {
        $prefixes = ['013', '014', '015', '016', '017', '018', '019'];
        return $prefixes[array_rand($prefixes)] . rand(10000000, 99999999);
    }
}
