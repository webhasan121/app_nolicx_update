<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartOrder extends Model
{
    //
    protected $fillable =
    [
        'user_id',
        'user_type',
        'belongs_to',
        'belongs_to_type',
        'order_id',
        'product_id',
        'quantity',
        'buying_price',
        'price', // normal price
        'total', // total multiple with quty
        'size',
        'status',
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::created(function (CartOrder $order) {
            // logger("Order Model Booted $order->id");
            // ProductComissionController::dispatchProductComissionsListeners($order->id);

            /**
             * if order created, then lower the product unit
             */
            $order->product->decrement('unit', $order->quantity);
            // $order->cartOrders()->each(function ($item) {
            // });

        });

        /**
         * if status updated to reject, then upper the product stock
         */
        static::updated(function (CartOrder $order) {
            if ($order->isDirty('status') && $order->status == 'Reject') {
                $order->product->increment('unit', $order->quantity);
            }
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeAccept($query)
    {
        return $query->where('status', 'Accept');
    }

    public function scopePicked($query)
    {
        return $query->where('status', 'Picked');
    }

    public function scopeDelivery($query)
    {
        return $query->where('status', 'Delivery');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'Delivered');
    }

    public function scopeHold($query)
    {
        return $query->where('status', 'Hold');
    }

    public function scopeCancel($query)
    {
        return $query->where('status', 'Cancel');
    }

    public function scopeConfirm($query)
    {
        return $query->where('status', 'Confirm');
    }



    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault([
            'id' => 0,
            'slug' => 'deleted-product',
            'name' => 'Deleted Product',
            'image' => 'default.png',
            'price' => 0,
        ]);
    }

    public function order()
    {
        return $this->belongsTo(order::class)->withDefault(
            [
                'id' => 0,
                'total' => 0,
                'status' => 'Deleted',
            ]
        );
    }

    /**
     * order may sync
     * when reseller get a resel product order
     * reseller re-order it to the vendor
     */
    public function syncDetails()
    {
        return $this->hasOne(syncOrder::class, 'user_cart_order_id', 'id');
    }
}
