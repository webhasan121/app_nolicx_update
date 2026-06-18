<?php

namespace App\Http\Controllers;

use App\Events\ProductComissions;
use App\Models\DistributeComissions;
use App\Models\Order;
use App\Models\Product;
use App\Models\ResellerResellProfits;
use App\Models\syncOrder;
use App\Models\TakeComissions;
use App\Models\UserHasRefs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Helpers\Helpers;

class ProductComissionController extends Controller
{
    public function deleteComissions(Request $request)
    {
        $tc = TakeComissions::where(['order_id' => $request->id])->exists();
        if ($tc) {
            TakeComissions::where(['order_id' => $request->id])->delete();
            DistributeComissions::where(['order_id' => $request->id])->delete();
            return redirect()->back()->with('success', 'Deleted comissions');
        } else {
            return redirect()->back()->with('error', 'Nothing Found or You need to role back first');
        }
    }

    public function deleteResellerProfit(Request $request)
    {
        $pc = ResellerResellProfits::where(['order_id' => $request->id])->exists();
        if ($pc) {
            ResellerResellProfits::where(['order_id' => $request->id])->delete();
            return redirect()->back()->with('success', 'Receller Profit Deleted');
        } else {
            return redirect()->back()->with('error', 'Nothing Found or You need to role back first');
        }
    }

    public static function dispatchProductComissionsListeners($id)
    {
        if (!TakeComissions::where(['order_id' => $id])->exists()) {
            # code...
            // echo 'hellow';
            // logger("ProductComissionsTake Function Called");
            try {
                $orderData = Order::findOrFail($id); // get order table
                $cartOrders = $orderData->cartOrders; // a single order has multiple products.
                $buyer = User::find($orderData->user_id); // product buyer
                $seller = User::find($orderData->belongs_to); // product seller

                if (!$buyer || !$seller) {
                    return;
                }

                $shop = [];

                switch ($orderData->belongs_to_type) {
                    case 'reseller':
                        // $shop = reseller::query(['user_id' => $orderData->belongs_to])->first('system_get_comission');
                        $shop = $seller?->resellerShop();
                        break;

                    case 'vendor':
                        // $shop = Vendor::query(['user_id' => $orderData->belongs_to])->first('system_get_comission');
                        $shop = $seller?->vendorShop();
                        break;
                }

                foreach ($cartOrders as $ord) {
                    $products = $ord->product; // get the relevent products from order details
                    // echo $products->id;

                    if (!$products || !$shop || !is_numeric($shop->system_get_comission) || (float) $shop->system_get_comission <= 0) {
                        continue;
                    }

                    $quantity = max(1, (int) ($ord->quantity ?? 1));
                    $lineTotal = is_numeric($ord->total)
                        ? (float) $ord->total
                        : ((float) ($ord->price ?? 0) * $quantity);
                    $lineBuyingTotal = self::lineBuyingTotal($ord, $products, $quantity);
                    $profit = round(max(0, $lineTotal - $lineBuyingTotal), 8);

                    if ($lineTotal <= 0 || $profit <= 0) {
                        continue;
                    }

                    $comission = round(($profit * (float) $shop->system_get_comission) / 100, 8); // system commission from seller profit
                    /**
                     * calculate the reseller profit
                     * if the seller is vendor
                     * and the order isnot purchase by reseller
                     */

                    if ($ord->user_type == 'reseller' && $ord->belongs_to_type == 'vendor' && $ord->order?->name == 'Resel') {
                        $rprofit = 0;
                        $p = ($ord->price - $ord->buying_price) * $ord->quantity;
                        $rprofit += $p;

                        $rrp = new  ResellerResellProfits();

                        DB::transaction(
                            function () use ($rrp, $ord, $rprofit) {
                                $rrp->forceFill(
                                    [
                                        'product_id' => $ord->product_id,
                                        'order_id' => $ord->order_id,
                                        'from' => $ord->belongs_to,
                                        'buy' => $ord->buying_price,
                                        'sel' => $ord->price,
                                        'to' => $ord->user_id,
                                        'profit' => round($rprofit, 8),
                                        'confirmed' => false,
                                    ]
                                );
                            }
                        );

                        if (!ResellerResellProfits::where(['order_id' => $ord->order_id])->exists()) {
                            # code...
                            $rrp->save();
                        }
                    }

                    /**
                     * calculate comissions
                     */
                    if ($comission > 0) {

                        // $slp = 0;
                        // if ($orderData->belongs_to_type == 'vendor') {
                        //     if ($orderData->name == 'Purchase') {
                        //         $slp = $products->totalPrice();
                        //     }else{

                        //     }
                        // } else {
                        //     $slp = $ord->total;
                        // }
                        // take the comissions and store in databse
                        $takeComissions = new TakeComissions();
                        $takeComissions->forceFill(
                            [
                                'user_id' => $ord->belongs_to,
                                'product_id' => $products->id,
                                'order_id' => $orderData->id,
                                'buying_price' => round($lineBuyingTotal, 8),
                                'selling_price' => $lineTotal,
                                'take_comission' => round($comission, 8),
                                'distribute_comission' => 0,
                                'store' => round($comission, 8),
                                'return' => round($profit - $comission, 8),
                                'profit' => round($profit, 8),
                                'confirmed' => false,
                                'comission_range' => $shop->system_get_comission,
                            ]
                        );

                        $takeComissions->save();

                        // Distribution rows are created only when the order is finished.
                    }
                }
                logger("ProductComissionsTake Done");
            } catch (\Throwable $th) {
                // throw $th;
                logger("ProductComissionsTakeError : $th");
            }
        }
    }

    private static function buildProductCommissionDistributions(Order $order, Product $product, float $comission): array
    {
        $actors = self::resolveProductCommissionActors($order, $product);
        $items = [];

        if ($actors['customer']) {
            $items[] = self::distributionItem($actors['customer'], 'Buyer Cashback', 10, $comission);
            $items = array_merge($items, self::referralDistributionItems($actors['customer'], 'Buyer', $comission));
        }

        if ($actors['reseller']) {
            $items[] = self::distributionItem($actors['reseller'], 'Reseller Commission', 10, $comission);
            $items = array_merge($items, self::referralDistributionItems($actors['reseller'], 'Reseller', $comission));
        }

        if ($actors['vendor']) {
            $items[] = self::distributionItem($actors['vendor'], 'Vendor Commission', 10, $comission);
            $items = array_merge($items, self::referralDistributionItems($actors['vendor'], 'Vendor', $comission));
        }

        return array_values(array_filter($items));
    }

    private static function resolveProductCommissionActors(Order $order, Product $product): array
    {
        $sync = syncOrder::query()
            ->with('userOrder')
            ->where('reseller_order_id', $order->id)
            ->first();

        $resel = $product->isResel;
        $mainProduct = $resel ? Product::find($resel->parent_id) : null;

        $customerId = $sync?->userOrder?->user_id ?? $order->user_id;
        $resellerId = $sync?->reseller_id
            ?? ($resel?->user_id)
            ?? ($order->belongs_to_type === 'reseller' ? $order->belongs_to : null)
            ?? ($order->user_type === 'reseller' ? $order->user_id : null);
        $vendorId = $sync?->vendor_id
            ?? ($mainProduct?->user_id)
            ?? ($product->belongs_to_type === 'vendor' ? $product->user_id : null)
            ?? ($order->belongs_to_type === 'vendor' ? $order->belongs_to : null);

        return [
            'customer' => $customerId ? User::find($customerId) : null,
            'reseller' => $resellerId ? User::find($resellerId) : null,
            'vendor' => $vendorId ? User::find($vendorId) : null,
        ];
    }

    private static function referralDistributionItems(User $user, string $side, float $comission): array
    {
        $items = [];
        $current = $user;
        $visited = [$user->id => true];
        $rules = [
            1 => ['range' => 5, 'info' => "{$side} Referrer Product Commission"],
            2 => ['range' => 1, 'info' => "{$side} Generation Level 1 Product Commission"],
            3 => ['range' => 1, 'info' => "{$side} Generation Level 2 Product Commission"],
            4 => ['range' => 1, 'info' => "{$side} Generation Level 3 Product Commission"],
        ];

        foreach ($rules as $rule) {
            $referrer = self::referrerOf($current);

            if (!$referrer || isset($visited[$referrer->id])) {
                break;
            }

            $visited[$referrer->id] = true;
            $items[] = self::distributionItem(
                $referrer,
                $rule['info'],
                $rule['range'],
                $comission
            );
            $current = $referrer;
        }

        return $items;
    }

    private static function referrerOf(User $user): ?User
    {
        if (!$user->reference) {
            return null;
        }

        $ref = UserHasRefs::query()
            ->where('ref', $user->reference)
            ->where('status', 1)
            ->first();

        if (!$ref || !$ref->user_id || (int) $ref->user_id === (int) $user->id) {
            return null;
        }

        return User::find($ref->user_id);
    }

    private static function distributionItem(?User $user, string $info, float $range, float $comission): ?array
    {
        if (!$user || !$user->id) {
            return null;
        }

        return [
            'user_id' => $user->id,
            'info' => $info,
            'range' => $range,
            'amount' => round(($comission * $range) / 100, 8),
        ];
    }

    private static function ensureResellerProfitForOrder(Order $order): void
    {
        if ($order->user_type !== 'reseller' || $order->belongs_to_type !== 'vendor' || $order->name !== 'Resel') {
            return;
        }

        $order->loadMissing('cartOrders');

        foreach ($order->cartOrders as $ord) {
            if (
                ResellerResellProfits::query()
                    ->where('order_id', $ord->order_id)
                    ->where('product_id', $ord->product_id)
                    ->exists()
            ) {
                continue;
            }

            $quantity = max(1, (int) ($ord->quantity ?? 1));
            $profit = round(((float) ($ord->price ?? 0) - (float) ($ord->buying_price ?? 0)) * $quantity, 8);

            if ($profit <= 0) {
                continue;
            }

            $rrp = new ResellerResellProfits();
            $rrp->forceFill([
                'product_id' => $ord->product_id,
                'order_id' => $ord->order_id,
                'from' => $ord->belongs_to,
                'buy' => $ord->buying_price,
                'sel' => $ord->price,
                'to' => $ord->user_id,
                'profit' => $profit,
                'confirmed' => false,
            ]);
            $rrp->save();
        }
    }

    private static function lineBuyingTotal($cartOrder, $product, int $quantity): float
    {
        if (is_numeric($cartOrder->buying_price)) {
            return (float) $cartOrder->buying_price * $quantity;
        }

        if ($product && is_numeric($product->buying_price)) {
            return (float) $product->buying_price * $quantity;
        }

        return 0;
    }

    public function refreshPendingOrderComissions(Order $order): void
    {
        if (TakeComissions::query()->where(['order_id' => $order->id])->confirmed()->exists()) {
            return;
        }

        DistributeComissions::query()->where(['order_id' => $order->id])->pending()->delete();
        TakeComissions::query()->where(['order_id' => $order->id])->pending()->delete();

        self::dispatchProductComissionsListeners($order->id);
    }

    public function confirmTakeComissions($id)
    {
        $order = Order::findOrFail($id);
        if ($order) {
            $this->refreshPendingOrderComissions($order);
            self::ensureResellerProfitForOrder($order);

            $tc = TakeComissions::query()->where(['order_id' => $id])->pending()->get(); // pending

            if ($tc) {
                foreach ($tc as $item) {
                    $distribute = $this->createDistributionsForTake($item);
                    $item->distribute_comission = round($distribute, 8);
                    $item->store = round((float) $item->take_comission - $distribute, 8);
                    $item->confirmed = true;
                    $item->save();
                }
            }

            if ($order->user_type == 'reseller') {
                // ResellerResellProfits::query()->where(['order_id' => $order->id])->pending()->update(['confirmed' => true]);
                $rcp = ResellerResellProfits::query()->where(['order_id' => $order->id])->pending()->get();
                foreach ($rcp as $rcpi) {

                    $rcpi->confirmed = true;
                    $rcpi->save();
                }
            }

            $this->confirmDeliveryChargeCommissions($order);
        }
    }

    public function confirmSingleTakeComissions($takeId)
    {
        try {
            $take = TakeComissions::query()->with('order')->find($takeId);

            if (!$take || !$take->order) {
                return;
            }

            $order = $take->order;
            $this->refreshPendingOrderComissions($order);
            self::ensureResellerProfitForOrder($order);

            $tc = TakeComissions::query()->where(['order_id' => $order->id])->pending()->get();

            if ($tc) {
                foreach ($tc as $item) {
                    $distribute = $this->createDistributionsForTake($item);
                    $item->distribute_comission = round($distribute, 8);
                    $item->store = round((float) $item->take_comission - $distribute, 8);
                    $item->confirmed = true;
                    $item->save();
                }
            }

            if ($order->user_type == 'reseller') {
                // ResellerResellProfits::query()->where(['order_id' => $order->id])->pending()->update(['confirmed' => true]);
                $rcp = ResellerResellProfits::query()->where(['order_id' => $order->id])->pending()->get();
                foreach ($rcp as $rcpi) {

                    $rcpi->confirmed = true;
                    $rcpi->save();
                }
            }

            $this->confirmDeliveryChargeCommissions($order);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function distributeComissions($id)
    {
        $distributes = DistributeComissions::query()
            ->where('order_id', $id)
            ->pending()
            ->groupBy('user_id')
            ->select('user_id', DB::raw('SUM(amount) as total_amount'))
            ->get();

        foreach ($distributes as $items) {
            UserWalletController::add($items->user_id, $items->total_amount);
        }
    }

    private function createDistributionsForTake(TakeComissions $take): float
    {
        $order = $take->order;

        if (!$order || (float) $take->take_comission <= 0) {
            return 0;
        }

        $product = Product::find($take->product_id);
        if (!$product) {
            return 0;
        }

        $distributions = self::buildProductCommissionDistributions($order, $product, (float) $take->take_comission);
        $existing = DistributeComissions::query()
            ->where('parent_id', $take->id)
            ->get()
            ->mapWithKeys(function (DistributeComissions $item) {
                return [$item->user_id . '|' . $item->info => true];
            });

        foreach ($distributions as $distribution) {
            $key = $distribution['user_id'] . '|' . $distribution['info'];
            if ($existing->has($key)) {
                continue;
            }

            $dcm = new DistributeComissions();
            $dcm->forceFill([
                'product_id' => $take->product_id,
                'order_id' => $take->order_id,
                'parent_id' => $take->id,
                'user_id' => $distribution['user_id'],
                'info' => $distribution['info'],
                'range' => $distribution['range'],
                'amount' => $distribution['amount'],
                'confirmed' => (bool) $take->confirmed,
            ]);
            $dcm->save();
        }

        return round((float) DistributeComissions::query()
            ->where('parent_id', $take->id)
            ->sum('amount'), 8);
    }

    private function confirmDeliveryChargeCommissions(Order $order): void
    {
        if ((float) ($order->shipping ?? 0) <= 0) {
            return;
        }

        $this->removeInvalidRiderDeliveryCommissions($order);

        if ($this->hasRiderDeliveryCommissions($order)) {
            return;
        }

        $cod = $order->hasRider()->latest('id')->first();
        $rider = $cod?->rider;

        if (!$rider || !$rider->id) {
            return;
        }

        $deliveryCommissionRate = is_numeric($cod->comission ?? null)
            ? (float) $cod->comission
            : (float) ($rider->isRider()?->comission ?? 0);

        if ($deliveryCommissionRate <= 0) {
            return;
        }

        $companyDeliveryIncome = round(((float) $order->shipping * $deliveryCommissionRate) / 100, 8);
        if ($companyDeliveryIncome <= 0) {
            return;
        }

        $current = $rider;
        $visited = [$rider->id => true];
        $rules = [
            ['range' => 5, 'info' => 'Rider Direct Referrer Delivery Commission'],
            ['range' => 1, 'info' => 'Rider Referral Level 1 Delivery Commission'],
            ['range' => 1, 'info' => 'Rider Referral Level 2 Delivery Commission'],
            ['range' => 1, 'info' => 'Rider Referral Level 3 Delivery Commission'],
        ];

        foreach ($rules as $rule) {
            $referrer = self::referrerOf($current);

            if (!$referrer || isset($visited[$referrer->id])) {
                break;
            }

            $visited[$referrer->id] = true;
            $amount = round(($companyDeliveryIncome * $rule['range']) / 100, 8);

            if ($amount > 0 && self::canReceiveRiderDeliveryCommission($referrer)) {
                $dcm = new DistributeComissions();
                $dcm->forceFill([
                    'product_id' => null,
                    'order_id' => $order->id,
                    'parent_id' => null,
                    'user_id' => $referrer->id,
                    'info' => $rule['info'],
                    'range' => $rule['range'],
                    'amount' => $amount,
                    'confirmed' => true,
                ]);
                $dcm->save();
            }

            $current = $referrer;
        }
    }

    private function hasRiderDeliveryCommissions(Order $order): bool
    {
        return DistributeComissions::query()
            ->where('order_id', $order->id)
            ->whereNull('parent_id')
            ->where('info', 'like', 'Rider%Delivery%')
            ->exists();
    }

    private function removeInvalidRiderDeliveryCommissions(Order $order): void
    {
        DistributeComissions::query()
            ->with('user')
            ->where('order_id', $order->id)
            ->whereNull('parent_id')
            ->where('info', 'like', 'Rider%Delivery%')
            ->get()
            ->each(function (DistributeComissions $commission) {
                if (self::canReceiveRiderDeliveryCommission($commission->user)) {
                    return;
                }

                if ($commission->confirmed) {
                    $commission->confirmed = false;
                    $commission->save();
                }

                $commission->delete();
            });
    }

    private static function canReceiveRiderDeliveryCommission(?User $user): bool
    {
        return (bool) $user?->hasRole('rider');
    }


    // public function distributeComissions($id)
    // {
    //     $order = Order::find($id);
    //     /**
    //      * get all distributed comissions related to this order is
    //      * a
    //      */
    //     try {

    //         TakeComissions::query()->where(['order_id' => $order->id])->pending()->update(['confirmed' => true]);


    //         if ($order->user_type == 'reseller') {
    //             // ResellerResellProfits::query()->where(['order_id' => $order->id])->pending()->update(['confirmed' => true]);
    //             $rcp = ResellerResellProfits::query()->where(['order_id' => $order->id])->pending();
    //             $rcp->confirmed = true;
    //             $rcp->save();
    //         }

    //         return redirect()->back()->with('success', 'Comissions Distributed');
    //     } catch (\Throwable $th) {
    //         redirect()->back()->with('error', 'eroro distribute comissions');
    //     }
    //     // $dc = $otc->distributes()->pending()->get();

    //     // if ($otc) {



    //     //     foreach ($otc as $item) {

    //     //         // take system comission form vendor sotore
    //     //         UserWalletController::remove($order->belongs_to, $item->take_comission);

    //     //         // distributes
    //     //         foreach ($item->distributes as $distribute) {

    //     //             // add the balance to targeted user
    //     //             UserWalletController::add($distribute->user_id, $distribute->amount);
    //     //             $distribute->confirmed = true;
    //     //             $distribute->save();
    //     //         }

    //     //         $item->confirmed = true;
    //     //         $item->save();
    //     //     }
    //     // }
    // }


    /**
     * 
     */
    public function transferResellerResellProfit($order_id)
    {
        $order = Order::find($order_id);
        $isResellerOrderToVendor = $order->user_type == 'reseller' ? true : false;

        if ($isResellerOrderToVendor) {
            $rrp = ResellerResellProfits::query()->where(['order_id' => $order_id])->get();
            $totalReturnableProfit = $rrp->sum('profit');

            // cut the balance from vendor
            $res = UserWalletController::remove($order->belongs_to, $totalReturnableProfit);
            if ($res['success']) {

                // add balance to reseller
                $res = UserWalletController::add($order->user_id, $totalReturnableProfit);
            }

            // udpate confirmed
            if ($res['success']) {
                ResellerResellProfits::query()->where(['order_id' => $order_id])->update(['confirmed' => true]);
            }
        }
        return redirect()->back();
    }


    /**
     * role back comissions
     */
    public function roleBackDistributedComissions($id)
    {
        $order = Order::find($id);
        try {


            $order = Order::find($id);
            $tc = TakeComissions::query()->where(['order_id' => $id])->confirmed()->get();
            if ($tc) {
                foreach ($tc as $item) {
                    $item->confirmed = false;
                    $item->save();
                }
            }

            if ($order->user_type == 'reseller') {
                // ResellerResellProfits::query()->where(['order_id' => $order->id])->confirmed()->update(['confirmed' => false]);
                $rcp = ResellerResellProfits::query()->where(['order_id' => $order->id])->confirmed()->get();
                foreach ($rcp as $rcpi) {

                    $rcpi->confirmed = false;
                    $rcpi->save();
                }
            }

            DistributeComissions::query()
                ->where('order_id', $order->id)
                ->whereNull('parent_id')
                ->where('info', 'like', 'Rider%Delivery%')
                ->confirmed()
                ->get()
                ->each(function (DistributeComissions $commission) {
                    $commission->confirmed = false;
                    $commission->save();
                });

            return redirect()->back()->with('success', 'Roleback comission');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'error to roleback comissions');
        }
    }


    /**
     * role back a single comissions
     */
    public function roleBackComissions()
    {
        //
    }


    /**
     * transfer reseller profit from vendor
     */
    public function refundResellerResellProfit($order_id)
    {
        $order = Order::find($order_id);
        $isResellerOrderToVendor = $order->user_type == 'reseller' ? true : false;
        if ($isResellerOrderToVendor) {
            $rrp = ResellerResellProfits::query()->where(['order_id' => $order_id, 'confirmed' => true])->get();
            $totalReturnableProfit = $rrp->sum('profit');

            // cut the balance from vendor
            $res = UserWalletController::add($order->belongs_to, $totalReturnableProfit);
            if ($res['success']) {

                // add balance to reseller
                $res = UserWalletController::remove($order->user_id, $totalReturnableProfit);
            }

            // udpate confirmed
            if ($res['success']) {
                ResellerResellProfits::where(['order_id' => $order_id])->update(['confirmed' => false]);
            }
        }

        return redirect()->back();
    }
}
