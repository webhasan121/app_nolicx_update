<?php

namespace App\Listeners;

use App\Events\ProductComissions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Models\ResellerResellProfits;

class CalculateResellerReselProfit
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductComissions $event): void
    {
        $order = $event->data;

        /**
         * if this is order from reseller to vendor for reselling
         * then, reseller must take comission for seling vendor product
         */
        $profit = 0;

        if ($order->user_type == 'reseller' && $order->belongs_to_type == 'vendor') {
            // cart order
            $co = $order->cartOrders;
            foreach ($co as $item) {
                $p = ($item->price - $item->buying_price) * $item->quantity;
                $profit += $p;

                $rrp = new  ResellerResellProfits();

                DB::transaction(
                    function () use ($rrp, $item, $profit) {
                        $rrp->forceFill(
                            [
                                'product_id' => $item->product_id,
                                'order_id' => $item->order_id,
                                'from' => $item->belongs_to,
                                'buy' => $item->buying_price,
                                'sel' => $item->price,
                                'to' => $item->user_id,
                                'profit' => $profit,
                                'confirmed' => false,
                            ]
                        );
                    }
                );

                $rrp->save();
            }
        }
    }
}
