<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use Illuminate\Support\Str;
class UpdateCategorySlugs extends Command
{
    // protected $signature = 'update:category-slugs';
    // protected $description = 'Update all category slugs based on their names';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:category-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all category slugs based on their names';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating category slugs...');

        $categories = Category::all();
        $updated = 0;

        foreach ($categories as $category) {
            $newSlug = Str::slug($category->name);

            if ($category->slug !== $newSlug) {
                $category->slug = $newSlug;
                $category->save();
                $this->line("Updated: {$category->name} -> {$newSlug}");
                $updated++;
            }
        }

        $this->info("Finished. Total updated: {$updated}");
    }
}
