<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\rider as riderModel;

class cod extends Model
{
    use SoftDeletes; // Use SoftDeletes trait for soft deletion functionality


    // boot method 
    protected static function booted(): void
    {
        parent::booted();
        static::updated(function (cod $cod) {
            // if the cod status is 'Completed', cut the amount from rider account and add to seller account
            if ($cod->status == 'Completed') {

                // when product reached to the buyer, then make the order status to 'Delivered'
                $order = Order::find($cod->order_id);
                if ($order) {
                    $order->status = 'Delivered';
                    $order->save();
                }


                $rider = User::find($cod->rider_id);
                $seller = User::find($cod->seller_id);
                if ($rider && $rider->abailCoin() >= $cod->total_amount) {
                    // cut due_amount from rider account, and add to seller account
                    $rider->coin -= $cod->due_amount;
                    $rider->save();

                    // if rider have comission, then cut it from rider account
                    if ($cod->comission > 0 && $cod->system_comission) {
                        $rider->coin -= $cod->system_comission;
                        $rider->save();
                    }
                    if ($seller) {
                        // cut due_amount from reseller account, and add to seller account
                        $seller->coin += $cod->due_amount;
                        $seller->save();
                    }

                    // add shipping amount to rider account
                    $rider->coin += $order->shipping;
                }
            }
        });
    }

    protected $guarded = []; // Allow mass assignment for all attributes

    // Pending scope, the status is Pending
    public function scopePending($query)
    {
        $query->where('status', 'Pending');
    }

    // accept scope
    public function scopeAccept($query)
    {
        $query->where('status', 'Received');
    }

    // complete scope
    public function scopeComplete($query)
    {
        $query->where('status', 'Completed');
    }

    // fail scope
    public function scopeReturned($query)
    {
        $query->where('status', 'Returned');
    }



    // relationships, accessors, or other model methods can be added here as needed
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id')->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted Seller',
                'email' => 'not found',
            ]
        );
    }
    public function order()
    {
        return $this->belongsTo(Order::class)->withDefault(
            [
                'id' => 0,
                'total' => 0,
                'status' => 'Deleted',
            ]
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted User',
                'email' => 'not found',
            ]
        );
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id', 'id')->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted Rider',
                'email' => 'not found',
            ]
        );
    }
}
