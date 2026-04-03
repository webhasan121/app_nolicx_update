<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class userDeposit extends Model
{
    //

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
}
