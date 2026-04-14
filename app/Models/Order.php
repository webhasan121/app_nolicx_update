<?php

namespace App\Models;

use App\Events\ProductComissions;
use App\Http\Controllers\ProductComissionController;
use App\Jobs\UpdateProductSalesIndex;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', // the user, who made the order
        'user_type', // user / reseller
        'belongs_to', // vendor or reseller id
        'belongs_to_type', // 1: vendor, 2: reseller
        // 'product_id',
        // 'size',
        'name',
        // 'price',
        'quantity',
        'number',
        'total',
        'status',
        'received_at',
        'area_condition',
        'district',
        'upozila',
        'location',
        'road_no',
        'house_no',
        'shipping',
        'delevery',
        'target_area'
        // 'buying_price'
    ];


    protected static function booted(): void
    {
        parent::booted();
        static::created(function (Order $order) {
            // logger("Order Model Booted $order->id");
            // ProductComissionController::dispatchProductComissionsListeners($order->id);

            /**
             * if order created, then lower the product unit
             */
            $order->cartOrders()->each(function ($item) {
                $item->product->decrement('unit', $item->quantity);
            });

            UpdateProductSalesIndex::dispatch();
        });

        static::updated(function (Order $order) {
            if ($order->isDirty('status')) {
                $order->cartOrders()->each(function ($item, $order) {
                    $item->status = $item->order->status;
                    $item->save();
                });
            }
        });
    }


    //////////////// 
    // Attributes //
    ///////////////
    protected function location(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value)
        );
    }
    protected function district(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value)
        );
    }
    protected function upozila(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value)
        );
    }

    public function user()
    {
        // return $this->belongsTo(User::class, 'user_id');
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => "user not found",
            'email' => "user not found",
            'password' => "user not found",
            'coin' => 0,
            'reference' => 0,
        ]);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'belongs_to')->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted Rider',
                'email' => 'not found',
            ]
        );
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
        // return $this->belongsTo(Product::class, 'product_id');
        return $this->belongsTo(Product::class, 'product_id')->withDefault([
            'id' => 0,
            'slug' => 'deleted-product',
            'name' => 'Deleted Product',
            'image' => 'default.png',
            'price' => 0,
        ]);
    }

    public function cartOrders()
    {
        return $this->hasMany(CartOrder::class);
    }

    public function hasRider()
    {
        return $this->hasMany(cod::class, 'order_id', 'id');
    }
    // public function comissions()
    // {
    //     return $this->hasMany(ComissionTracking::class);
    // }

    // public function scopeAccepted($query)
    // {
    //     return $query->where('status', 'Confirm');
    // }


    public function comissionsInfo()
    {
        return $this->hasMany(TakeComissions::class);
    }

    public function comissionsDistributor()
    {
        return $this->hasMany(DistributeComissions::class);
    }

    public function resellerProfit()
    {
        return $this->hasMany(ResellerResellProfits::class);
    }


    /**
     * order belongs to a shop
     */
    public function shop()
    {
        if ($this->belongs_to_type == 'reseller') {
            return $this->belongsTo(reseller::class, 'belongs_to', 'user_id');
        }

        if ($this->belongs_to_type == 'vendor') {
            return $this->belongsTo(vendor::class, 'belongs_to', 'user_id');
        }
    }


    /**
     * order may sync
     * when reseller get a resel product order
     * reseller re-order it to the vendor
     */
    public function syncDetails()
    {
        return $this->hasOne(syncOrder::class, 'reseller_order_id', 'id');
    }
}
