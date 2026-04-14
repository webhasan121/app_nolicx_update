<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    protected $guarded = [];

    public function ta()
    {
        return $this->hasMany(ta::class, 'city_id');
    }
}
