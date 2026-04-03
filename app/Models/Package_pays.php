<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package_pays extends Model
{
    protected $fillable =
    [
        'package_id',
        'pay_type',
        'pay_to'
    ];

    public function packages()
    {
        return $this->belongsTo(Packages::class, 'package_id', 'id');
    }
}
