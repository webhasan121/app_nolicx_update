<?php

namespace App\Listeners;

use App\Events\ProductComissions;
use App\Http\Controllers\ProductComissionController;
use App\Models\DistributeComissions;
use App\Models\reseller;
use App\Models\TakeComissions;
use App\Models\User;
use App\Models\vendor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Code\Throwable;

class TakeProductComissions
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
        // echo 'hellow';
        $orderData = $event->data; // get order table
        $cartOrders = $orderData->cartOrders; // a single order has multiple products.
        $buyer = User::find($orderData->user_id); // product buyer
        $seller = User::find($orderData->belongs_to); // product seller

        $shop = [];

        switch ($seller?->account_type()) {
            case 'reseller':
                // $shop = reseller::query(['user_id' => $orderData->belongs_to])->first('system_get_comission');
                $shop = $seller?->resellerShop();
                break;

            case 'vendor':
                // $shop = vendor::query(['user_id' => $orderData->belongs_to])->first('system_get_comission');
                $shop = $seller->vendorShop();
                break;
        }

        foreach ($cartOrders as $ord) {
            $products = $ord->product; // get the relevent products from order details
            // echo $products->id;
            $profit = ($ord->price - $ord->buying_price) * $ord->quantity; // total profit of selling product
            $comission = round(($profit * $shop->system_get_comission) / 100, 2); // system comissions take form the reseller/vendor
            $distribute = round(($comission * 30) / 100, 2);

            if ($products && $shop->system_get_comission) {

                // take the comissions and store in databse
                $takeComissions = new TakeComissions();
                DB::transaction(function ()
                use ($takeComissions, $ord, $profit, $comission, $orderData, $distribute, $shop, $products) {
                    $takeComissions->forceFill(
                        [
                            'user_id' => $ord->belongs_to,
                            'product_id' => $products->id,
                            'order_id' => $orderData->id,
                            'buying_price' => $products->buying_price,
                            'selling_price' => $ord->total, // 
                            'take_comission' => $comission,
                            'distribute_comission' => $distribute,
                            'store' => round($comission - $distribute, 2),
                            'return' => $profit - $comission,
                            'profit' => $profit,
                            'confirmed' => false,
                            'comission_range' => $shop->system_get_comission,
                        ]
                    );
                });
                $takeComissions->save();

                // distribute the comissions
                // if $takeComissions id geet
                if ($takeComissions->id) {

                    /**
                     * comission distributed among those ....
                     * 
                     * buyer
                     * buyer referrer user
                     * seller and 
                     * seller reffer user
                     */
                    $data = array(
                        'buyer' => $buyer,
                        'buyerRef' => $buyer->getReffOwner->owner,
                        'seller' => $seller,
                        'sellerRef' => $seller->getReffOwner->owner,
                    );

                    // $distributeData = array(
                    //     'product_id' => $products->id,
                    //     'order_id' => $orderData->id,
                    //     'parent_id' => $ord->id,
                    //     $data,
                    // );

                    foreach ($data as $key => $item) {
                        $dcm = new DistributeComissions();
                        $info = '';
                        $am = '';
                        $rng = '';


                        if ($key == 'buyer' || $key == 'seller') {
                            $rng = 10;
                            $am = round(($comission * $rng) / 100, 2);
                        } else {
                            $rng = 5;
                            $am = round(($comission * $rng) / 100, 2);
                        }

                        switch ($key) {
                            case 'buyer':
                                $info = 'Purchase Product';
                                break;
                            case 'seller':
                                $info = 'Sel Product';
                                break;

                            case 'buyerRef':
                                $info = 'Ref User Purchase Product';
                                break;

                            case 'sellerRef':
                                $info = 'Ref Uer Sell Product';
                                break;

                            default:
                                $info = 'Comissions';
                                break;
                        }

                        DB::transaction(
                            function ()
                            use ($item, $dcm, $products, $orderData, $info, $am, $rng, $takeComissions) {
                                $dcm->forceFill(
                                    [
                                        'product_id' => $products->id,
                                        'order_id' => $orderData->id,
                                        'parent_id' => $takeComissions->id,
                                        'user_id' => $item->id,
                                        'info' => $info,
                                        'range' => $rng,
                                        'amount' => $am,
                                    ]
                                );
                            }
                        );

                        $dcm->save();
                    }

                    /**
                     * with this event and listeners
                     * all the comissions are calculated and stored to database for next process
                     * this proess just calculate, not cut or add comission to user
                     * 
                     * comission add or removed via another method called at the time of accept order
                     */

                    // $productComissionController = new ProductComissionController();
                    // $productComissionController->distributeComissions($ord->id);

                    // DB::transaction(function () use ($distributeComissions) {});
                }
            }
        }
    }


    /**
     * Handle a job failure.
     */
    public function failed(ProductComissions $event, Throwable $exception): void
    {
        Log::error($exception);
        throw $exception;
    }
}
