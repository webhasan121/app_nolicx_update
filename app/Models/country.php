<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    protected $guarded = [];

    public function states()
    {
        return $this->hasMany(state::class, 'country_id');
    }
}
