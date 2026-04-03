<?php

namespace App\Http\Controllers;

use App\HandleReseller;
use App\Http\Middleware\AbleTo;
use App\Models\Product;
use App\Models\Reseller_resel_product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ResellerController extends Controller
{
    // use HandleReseller;
    // public function __construct()
    // {
    //     $this->middleware(AbleTo::class . ":product_view")->only('productIndex');
    // }

    public function cloneProducts($target_product_id, $your_price, $cat)
    {
        try {
            $target_product = Product::findOrFail($target_product_id)->toArray();
            $bt = $target_product['user_id'];
            if ($target_product) {
                $target_product['price'] = $your_price ?? 0;
                $target_product['discount'] = 0;
                $target_product['offer_type'] = 0;
                $target_product['buying_price'] = $target_product['price'];
                $target_product['unit'] = 0;
                $target_product['category'] = $cat;
                $target_product['belongs_to_type'] = 'reseller';
                $target_product['user_id'] = Auth::id();
                $target_product['status'] = 'Draft';
                $target_product['display_at_home'] = null;
                $target_product['created_at'] = now();
                $target_product['updated_at'] = now();
                $target_product['country'] = Auth::user()->country ?? null;
                $target_product['deleted_at'] = null;


                $pdi = 0;
                foreach ($target_product as $key => $value) {
                    $pdi = Product::create(
                        [
                            $key => $value
                        ]
                    );
                }
                if ($pdi) {
                    $rr = new Reseller_resel_product();
                    $rr->user_id = Auth::id();
                    $rr->belongsTo = $bt;
                    $rr->product_id = $pdi;
                    $rr->parent_id = $target_product['id'];
                    $rr->save();
                }
                return redirect()->route('vendor.products.edit', ['product' => $rr->id])->with('success', "Cloned to your product List.");
            } else {
                return redirect()->back()->with('error', "Can't clone Product now");
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "Error While cloning Product");
        }
    }


    public function productOrder(Request $req)
    {
        $validate = $req->validate(
            [
                'name' => 'reqired',
                'phone' => 'required',
                'size' => 'required',
                'price' => 'required',
                'quantity' => 'required',
                'location' => 'required',
                'district' => 'required',
                'upozila' => 'required',
                'delevery' => 'required',
                'area_condition' => 'required',
                'road_no' => 'required',
                'house_no' => 'required',
            ]
        );



        $product = Product::where(['id' => $req->product_id, 'belongs_to_type' => 'vendor'])->first();
        $data = array(
            'user' => Auth::id(),
            'user_type' => 'reseller',
            'belongs_to' => $product->user_id,
            'belongs_to_type' => 'vendor',
            'status' => 'Pending',

        );
    }
}
