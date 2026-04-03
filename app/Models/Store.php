<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['name', 'coin'];


    /**
     * function return the donation name of coin store
     */
    public function scopeDonation($query)
    {
        return $query->where('name', 'donation');
    }

    public function scopeCost($query)
    {
        return $query->where('name', 'server_cost');
    }

    public function scopeStore($query)
    {
        return $query->where('name', 'store');
    }

    public function scopeComission($query)
    {
        return $query->where('name', 'comission');
    }
    
    public function scopeProfit($query)
    {
        return $query->where('name', 'profit');
    }
}
