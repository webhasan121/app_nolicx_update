<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateProductSalesIndex as getIndex;

class UpdateProductSalesIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-product-sales-index';

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
        getIndex::dispatch();
    }
}
