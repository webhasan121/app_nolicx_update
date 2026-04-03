<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class MakeFakeOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-fake-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a fake order to check is the shpping addresh rider can get the order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // make a fake order belongs to a product
        Order::create(
            
        );
    }
}
