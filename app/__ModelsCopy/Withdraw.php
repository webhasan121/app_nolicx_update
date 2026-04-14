<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    //
    protected $guarded = [];


    /**
     * scope to bind is rejected field
     */
    public function scopeNotRejected($query)
    {
        return $query->whereNull('is_rejected');
    }

    /**
     * scope to bind is rejected field
     */
    public function scopeRejected($query)
    {
        return $query->whereNotNull('is_rejected');
    }

    /**
     * scope to bind is rejected field
     */
    public function scopeAccepted($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('is_rejected')
                ->where('status', 1);
        });
    }

    /**
     * scope to bind is rejected field
     */
    public function scopePending($query)
    {
        return $query->whereNull('is_rejected')->where('status', 0);
    }


    /**
     * scope to bind is rejected field
     */
    // public function scopeAuth($query)
    // {
    //     return $query->where('user_id', auth()->user()->id());
    // }


    /**
     * method define relation to user
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
     * 
     */
    // public function transactions()
    // {
    //     return $this->belongsTo(withdraw_transactions::class, 'id', 'withdraw_id');
    // }
}
