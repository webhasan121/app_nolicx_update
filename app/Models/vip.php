<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class vip extends Model
{
    use SoftDeletes;
    //
    protected $fillable =
    [

        'name',
        'phone',
        'nid_front',
        'nid_back',
        'nid',
        'payment_by',
        'trx',
        'user_id',
        'package_id',
        'status', //pending, confirmed,
        'valid_till', //a package valid for 360 days
        'valid_from', //renew date
        'task_type', //montyly, daily

        'reference', //if user register with reference
        'comission', //if user register with reference, the comission for the reference user
        'refer', //if user register with reference, the refer user id

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted User',
                'email' => 'not found',
            ]
        );
    }

    public function referBy()
    {
        return $this->belongsTo(User::class, 'refer', 'id')->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted User',
                'email' => 'not found',
            ]
        );
    }

    public function package()
    {
        return $this->belongsTo(Packages::class)->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted Package',
                'email' => 'not found',
            ]
        );
    }

    /**
     * @return active vip
     */
    public function active()
    {
        return $this->status == 1;
    }

    /**
     * method return the active query
     * @return vip
     */
    public function scopeActive($query)
    {
        return $query->where(['status' => 1]);
    }

    /**
     * method add the valid scope
     */
    public function scopeValid($query)
    {
        return $query->where('valid_till', '>', today());
    }

    /**
     * method add the valid scope
     */
    public function scopeInValid($query)
    {
        return $query->where('valid_till', '<', today());
    }
}
