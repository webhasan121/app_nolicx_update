<?php

namespace App\Models;

use App\Http\Controllers\UserWalletController;
use Illuminate\Database\Eloquent\Model;

class ResellerResellProfits extends Model
{
    //

    protected static function booted()
    {
        static::updated(function ($ResellerResellProfits) {
            if ($ResellerResellProfits->isDirty('confirmed') && $ResellerResellProfits->confirmed == true) {
                UserWalletController::remove($ResellerResellProfits->from, $ResellerResellProfits->profit); // reduce balance form vendor
                UserWalletController::add($ResellerResellProfits->to, $ResellerResellProfits->profit); // add balance to reseller
            }

            if ($ResellerResellProfits->isDirty('confirmed') && $ResellerResellProfits->confirmed == false) {
                UserWalletController::add($ResellerResellProfits->from, $ResellerResellProfits->profit); // reduce balance form vendor
                UserWalletController::remove($ResellerResellProfits->to, $ResellerResellProfits->profit); // add balance to reseller
            }
        });
    }

    /**
     * scope
     */
    public function scopePending($query)
    {
        return $query->where(['confirmed' => false]);
    }

    public function scopeConfirmed($query)
    {
        return $query->where(['confirmed' => true]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to');
    }
}
