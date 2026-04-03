<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Products_has_comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    /**
     * @param App\Models\product
     * @param comments
     * 
     * 
     * @return json
     */
    public function addCommentsToProduct(Request $req)
    {
        // $product = Product::findOrFAil($req->product_id);
        $req->validate(
            [
                'comments' => ['required', 'max:500'],
            ]
        );

        try {
            DB::transaction(function ()
            use ($req) {
                $comments = new Products_has_comments();
                $comments->forceFill(
                    [
                        'product_id' => $req->product_id,
                        'user_id' => Auth::id(),
                        'comments' => $req->comments,
                    ]
                );
                $comments->save();
            });
            return response()->json(['success' => true, 'message' => 'Comment Added !'], 200);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 200);
        }
    }



    /**
     * route method
     */
    public function storeComment(Request $req)
    {
        // $product = Product::findOrFAil($req->product_id);
        $req->validate(
            [
                'comments' => ['required', 'max:500'],
            ]
        );

        try {
            DB::transaction(function ()
            use ($req) {
                $comments = new Products_has_comments();
                $comments->forceFill(
                    [
                        'product_id' => $req->product_id,
                        'user_id' => Auth::id(),
                        'comments' => $req->comments,
                    ]
                );
                $comments->save();
            });
            return redirect()->back()->with('success', 'comment added !');
        } catch (\Throwable $th) {
            return redirect()->back();
            // return response()->json(['success' => false, 'message' => $th->getMessage()], 200);
        }
    }
}
