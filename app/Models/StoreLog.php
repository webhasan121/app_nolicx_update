<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreLog extends Model
{
    protected $fillable = ['store_id', 'order_id', 'amount'];
}
