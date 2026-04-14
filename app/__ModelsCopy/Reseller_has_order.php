<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reseller_has_order extends Model
{
    protected $fillable =
    [
        'name',
        'phone',
        'user_id',
        'belongs_to',
        'quantity',
        'total',
        'status', // 
        'district',
        'upozila',
        'location',
        'house_no',
        'road_no',
        'area_condition', // Inside Dhaka or outside
        'shipping',
        'delevery', // courier or home
        'note', 
    ];


    //////////////// 
    // RELATION //
    ///////////////

    public function reseller()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'belongs_to');
    }

    public function cartOrders()
    {
        return $this->hasMany(Reseller_order_details::class, 'order_id', 'id');
    }


    //////////////// 
    // scope //
    ///////////////
    public function scopeAccep($query)
    {
        return $query->where(['status' => 'Accepted']);
    }
    public function scopePending($query)
    {
        return $query->where(['status' => 'Pending']);
    }
    public function scopeReject($query)
    {
        return $query->where(['status' => 'Rejected']);
    }
    public function scopeCancel($query)
    {
        return $query->where(['status' => 'Cancel']);
    }
}
