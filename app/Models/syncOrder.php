<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class syncOrder extends Model
{

    // booted 
    protected static function booted(): void
    {
        parent::booted();
        // static::created(function (Order $order) {
        //     logger("Order Model Booted $order->id");
        //     ProductComissionController::dispatchProductComissionsListeners($order->id);
        // });

        static::updated(function ($data) {
            // logger("Order Model Updated $data->id");
            // if the status field if updated
            if ($data->isDirty('status')) {
                // logger("Order Model Updated $data->id");
                Order::where(['id' => $data->user_order_id])->update(['status' => $data->status]);
            }
        });
    }


    public function resellerOrder()
    {
        return $this->belongsTo(Order::class,  'reseller_order_id', 'id');
    }

    public function userOrder()
    {
        return $this->belongsTo(Order::class, 'user_order_id', 'id');
    }
}
