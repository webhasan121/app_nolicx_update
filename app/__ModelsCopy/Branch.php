<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model {
    use SoftDeletes;

    protected $fillable = [ 'name', 'email', 'phone', 'slug', 'address', 'type' ];
}
