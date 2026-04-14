<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Navigations extends Model
{
    protected $fillable = ['name', 'status'];

    // status active scope
    public function scopeShow($query)
    {
        return $query->where(['status' => 1]);
    }

    // scope inacrive scope
    public function scopeHide($query)
    {
        return $query->where(['status' => 0]);
    }


    // has many links
    public function links()
    {
        return $this->hasMany(Navigations_has_link::class);
    }
}
