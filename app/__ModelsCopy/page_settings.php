<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class page_settings extends Model
{
    protected $fillable =
    [
        'name',
        'content',
        'status',
        'slug',

        'title',
        'keyword',
        'thumbnail',
        'description'
    ];
}
