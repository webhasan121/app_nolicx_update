<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Casts\Attributes;
use Illuminate\Support\Str;

use function Pest\Laravel\json;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'title',
        'slug',
        'description',
        'price',
        'discount',
        'buying_price',
        'category_id',
        'user_id',
        'belongs_to_type', // vendor or reseller
        'thumbnail',
        'offer_type',
        'unit',
        'status', // 
        'display_at_home',

        'vc',
        'brand',
        'country',
        'state',

        'cod',
        'couried',
        'hand',

        'shipping_in_dahka',
        'shipping_out_dhaka',
        'shipping_note',

        'badge',
        'tags',
        'accept_cupon',

        'meta_title',
        'meta_description',
        'keyword',
        'meta_tags',
        'meta_thumbnail',
    ];


    public function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'badge' => 'array',
            'tags' => 'array',
        ];
    }

    // attributes 
    protected function title(): Attributes
    {
        // make the title Str::title()
        return Attributes::make(
            get: fn($value) => Str::title($value),
        );
    }


    /**
     * give user default 'user' role 
     * when model is created
     */
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Product $product) {
            $product->user_id = Auth::id();
            $product->status = 'Active';
        });

        static::created(function (Product $product) {
            product_has_attribute::create(
                [
                    'product_id' => $product->id,
                ]
            );
        });


        static::updated(function (Product $product) {

            if ($product->belongs_to_type == 'vendor' && $product->resel) {
                $rp = Product::query()->whereIn('id', $product->resel?->pluck('product_id'));


                /**
                 * if update unit, the update the uni of resel product
                 */
                if ($product->isDirty('unit')) {
                    $rp->update(['unit' => $product->unit]);
                }


                /**
                 * if delet, then delet the resel product
                 */
                $rp->update(['deleted_at' => $product->deleted_at ?? null]);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'id' => 0,
            'name' => "Category Not Found!",
        ]);
    }

    //////////////// 
    // SCOPE //
    ///////////////
    public function scopeActive($query)
    {
        return $query->where(['status' => 'Active']);
    }

    public function scopeDraft($query)
    {
        return $query->where(['status' => 'Draft']);
    }

    public function scopeReseller($query)
    {
        return $query->where(['belongs_to_type' => 'reseller']);
    }

    public function scopeVendor($query)
    {
        return $query->where(['belongs_to_type' => 'vendor']);
    }
    // public function scopeDisabled($query)
    // {
    //     return $query->where(['status' => 'Disabled']);
    // }

    // public function comissions()
    // {
    //     return $this->hasMany(ComissionTracking::class);
    // }

    public function scopeHome($query)
    {
        return $query->whereNotNull('display_at_home');
    }

    public function attr()
    {
        return $this->hasOne(product_has_attribute::class);
    }
    public function showcase()
    {
        return $this->hasMany(product_has_image::class);
    }

    public function totalPrice()
    {
        return $this->offer_type ? $this->discount : $this->price;
    }

    /**
     * Product has many order
     */
    public function orders()
    {
        return $this->hasMany(CartOrder::class, 'product_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted Owner',
                'email' => 'not found',
            ]
        );
    }

    /**
     * product has comission take and distributors
     */
    public function comissionsTake()
    {
        return $this->hasMany(TakeComissions::class);
    }
    public function comissionsDistributor()
    {
        return $this->hasMany(DistributeComissions::class);
    }

    /**
     * determined is the  product resell for reseller
     */
    public function isResel()
    {
        return $this->hasOne(Reseller_resel_product::class, 'product_id', 'id');
    }

    /**
     * fined by whon product is being resell    
     */
    public function resel()
    {
        return $this->hasMany(Reseller_resel_product::class, 'parent_id', 'id');
    }

    // public function reselProducts()
    // {
    //     return $this->belongsToMany(Product::class, 'reseller_resel_products', 'parent_id', 'product_id');
    // }

    /**
     * comments
     */
    public function comments()
    {
        return $this->hasMany(Products_has_comments::class);
    }


    public function syncOrder()
    {
        return $this->hasMany(syncOrder::class, 'vendor_product_id');
    }
}
