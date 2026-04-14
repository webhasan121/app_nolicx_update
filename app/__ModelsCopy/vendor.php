<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\vendor_has_document;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class vendor extends Model
{
    //
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

        // business type 
        // 'business_type', // Corporation, LLC, Sole Provider, Partnership, Other

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

    //////////////// 
    // MODEL BOOT //
    ///////////////
    protected static function boot()
    {
        parent::boot();
        // static::observe(ShopObserver::class);

        /**
         * if model inserte
         */
        static::creating(function ($model) {
            $model->status = 'Pending';
            $model->user_id = Auth::id();
        });

        // static::created

        static::created(function ($model) {
            // add new documents 
            vendor_has_document::create(['user_id' => Auth::id(), 'vendor_id' => $model->id]);

            // add new nomini
            vendor_has_nomini::create(['user_id' => Auth::id(), 'vendor_id' => $model->id]);

            $model->documents()->update(['deatline' => Carbon::now()->addDays(7)]);

            // Session::flash('Success', "Model Created !");
        });

        /**
         * if model saving and have status = 1
         */
        static::saving(function ($model) {
            if ($model->isDirty('status') && $model->status == 'Active') {
                $model->is_rejected = 0;
                $model->rejected_for = null;

                $model->documents()->update(['deatline' => null]);
            }

            if ($model->isDirty('status') && $model->status != 'Pending') {
                $model->is_rejected = 0;
            }

            /**
             * if rejected
             */
            if ($model->isDirty('is_rejected') && $model->is_rejected) {
                $model->status = "Suspended";
                // $model->rejected_for = $request;
            }
        });

        static::updated(function (vendor $rider) {
            /**
             * if the status field is updated,
             * and status is Active,
             * then assign the rider role
             */

            // get the rider role
            $riderRoleName =  Role::where('name', 'rider')->first();
            if ($rider->isDirty('status') && $rider->status == 'Active') {
                // assign role to user
                $rider->user?->assignRole($riderRoleName);
            } else {

                // else remove the role if exists
                if ($rider->user?->hasRole($riderRoleName)) {
                    $rider->user?->removeRole($riderRoleName);
                }
            }
        });
    }


    /**
     * cast information_update_date to datetime
     */
    protected $casts = [
        'information_update_date' => 'datetime',
    ];




    //////////////// 
    // SCOPE //
    ///////////////
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




    //////////////// 
    // RELATION //
    ///////////////


    /**
     * model belongs to a user
     */
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

    /**
     * model has document
     * vendor_has_document table
     */
    public function documents()
    {
        return $this->hasOne(vendor_has_document::class);
    }
}
