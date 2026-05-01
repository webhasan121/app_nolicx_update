<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vip extends Model
{
    use SoftDeletes;

    protected $table = 'vips';

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
        'status',
        'valid_till',
        'valid_from',
        'task_type',

        'reference',
        'comission',
        'refer',
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
     * @return Vip
     */
    public function scopeActive($query)
    {
        return $query->where(['status' => 1]);
    }

    public function scopeValid($query)
    {
        return $query->where('valid_till', '>', today());
    }

    public function scopeInValid($query)
    {
        return $query->where('valid_till', '<', today());
    }
}
