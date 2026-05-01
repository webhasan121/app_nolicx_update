<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable = [
        'user_id',
        'shop_name_bn',
        'shop_name_en',
        'slug',
        'description',
        'logo',
        'banner',

        // business address and contact
        'phone',
        'email',
        'country',
        'district',
        'upozila',
        'village',
        'zip',
        'road_no',
        'house_no',
        'address',

        // has many reference
        'ref_to_company',
        'ref_name',
        'ref_contact',

        // authorization
        'is_rejected',
        'rejected_for',
        'system_get_comission',
        'information_update_date',
        'status',

        'allow_max_product_upload',
        'max_product_upload',

        'prevent_montyly_product_upload',
        'max_product_upload_monthly',

        'prevent_daily_product_upload',
        'max_product_upload_daily',

        'can_resell_products',
        'can_accept_reseller_order',

        'fixed_amount',
    ];

    protected $casts = [
        'information_update_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->status = 'Pending';
            $model->user_id = Auth::id();
        });

        static::created(function ($model) {
            VendorHasDocument::create(['user_id' => Auth::id(), 'vendor_id' => $model->id]);
            VendorHasNomini::create(['user_id' => Auth::id(), 'vendor_id' => $model->id]);

            $model->documents()->update(['deatline' => Carbon::now()->addDays(7)]);
        });

        static::saving(function ($model) {
            if ($model->isDirty('status') && $model->status == 'Active') {
                $model->is_rejected = 0;
                $model->rejected_for = null;

                $model->documents()->update(['deatline' => null]);
            }

            if ($model->isDirty('status') && $model->status != 'Pending') {
                $model->is_rejected = 0;
            }

            if ($model->isDirty('is_rejected') && $model->is_rejected) {
                $model->status = "Suspended";
            }
        });

        static::updated(function (Vendor $vendor) {
            $vendorRole = Role::where('name', 'rider')->first();
            if ($vendor->isDirty('status') && $vendor->status == 'Active') {
                $vendor->user?->assignRole($vendorRole);
            } else {
                if ($vendor->user?->hasRole($vendorRole)) {
                    $vendor->user?->removeRole($vendorRole);
                }
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 'Active');
    }

    public function scopePending($query)
    {
        return $query->where('status', '=', 'Pending');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', '=', 'Suspended');
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', '=', 'Disabled');
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

    public function documents()
    {
        return $this->hasOne(VendorHasDocument::class);
    }
}
