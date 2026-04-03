<?php

namespace App\Console\Commands;

use App\Models\city;
use App\Models\state;
use App\Models\ta;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\country;

class CopyCityNameToArea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:targeted-areas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stateId = state::where('country_id', country::where('name', 'Bangladesh')->first()?->id)->pluck('id');
        
        // log the stateId

        $cities = city::whereIn('state_id', $stateId)->get();
        ta::query()->delete();
        $cities->each(function ($city) {
            // first clear whole ta table

            // then insert all cities to ta table
            
            ta::create([
                'name' => $city->name,
                'slug' => Str::slug($city->name),
                'city_id' => $city->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
