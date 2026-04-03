<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_has_address extends Model
{
    protected $fillable =
    [
        // new user_has_address
        'user_id',
        'label', // home, office, shop
        'is_default', // 
        'country',
        'country_code',
        'zip',
        'state',
        'city',
        'line1',
        'line2',
        'phone',
        'phone2',
        'phone3',
    ];

    /**
     * Relation to a single user
     * 
     * @belongsTo Users
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
