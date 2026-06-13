<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ta extends Model
{
    protected $guarded = [];

    public function city()
    {
        return $this->belongsTo(city::class, 'city_id');
    }
}
