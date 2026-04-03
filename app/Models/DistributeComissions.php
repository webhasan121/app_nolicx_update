<?php

namespace App\Models;

use App\Http\Controllers\UserWalletController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistributeComissions extends Model
{
    // use SoftDeletes;
    //

    protected static function booted(): void
    {
        // parent::boot();
        static::updated(function ($distributeComissions) {
            $takeCom = $distributeComissions->take;


            if ($distributeComissions->isDirty('confirmed')) {
                try {
                    //code...
                    if ($distributeComissions->confirmed == true) {
                        UserWalletController::add($distributeComissions->user_id, $distributeComissions->amount);
                    } elseif ($distributeComissions->confirmed == false) {
                        UserWalletController::remove($distributeComissions->user_id, $distributeComissions->amount);
                    }
                } catch (\Throwable $th) {
                    // $distributeComissions->confirmed = false;
                    // $distributeComissions->save();
                }
            }

            // if (DistributeComissions::where(['parent_id' => $distributeComissions->parent_id])->confirmed()->count() == 0) {
            //     $takeCom->confirmed = false;
            //     $takeCom->save();
            // }
        });
    }


    // cast 
    protected $casts = [
        'confirmed' => 'boolean',
        'amount' => 'float',
        'take_comission' => 'float',
        'distribute_comission' => 'float',
        'store' => 'float',
        'return' => 'float',
        'profit' => 'float',
    ];



    /**
     * scope
     */

    public function scopePending($query)
    {
        return $query->where(['confirmed' => false]);
    }

    public function scopeConfirmed($query)
    {
        return $query->where(['confirmed' => 1]);
    }


    /**
     * relationship
     */
    public function take()
    {
        return $this->belongsTo(TakeComissions::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
