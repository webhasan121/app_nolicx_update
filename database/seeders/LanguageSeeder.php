<?php

namespace Database\Seeders;

use App\Support\TranslationManager;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        TranslationManager::bootstrapDefaults();
    }
}
