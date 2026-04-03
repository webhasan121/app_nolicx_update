<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class UpdateProductSalesIndex implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // the the insigh
        $topSellingProducts = DB::table('cart_orders')
            ->where('user_type', '=', 'user')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->get();

        foreach ($topSellingProducts as $item) {
            DB::table('product_sales_indices')->updateOrInsert(
                ['product_id' => $item->product_id],
                ['total_sales' => $item->total_sold, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
