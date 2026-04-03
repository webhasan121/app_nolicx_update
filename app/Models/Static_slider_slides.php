<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Static_slider_slides extends Model
{
    protected $fillable = [
        'slider_id',
        'image',
        'status',
        'action_type',
        'action_url',
        'action_target',
    ];


    public function slider()
    {
        return $this->belongsTo(Static_slider::class, 'id', 'slider_id');
    }
}
