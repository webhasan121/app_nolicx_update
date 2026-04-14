<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class state extends Model
{
    protected $guarded = [];

    public function country() {
        return $this->belongsTo(country::class, 'country_id', 'id');
    }

    public function cities()
    {
        return $this->hasMany(city::class, 'state_id');
    }
}
