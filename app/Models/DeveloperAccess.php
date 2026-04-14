<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeveloperAccess extends Model {
    protected $fillable = [ 'applied_id', 'commission', 'message', 'status', 'response_by' ];

    public function user() {
        return $this->belongsTo(User::class, 'applied_id', 'id');
    }

    public function responder() {
        return $this->belongsTo(User::class, 'response_by', 'id');
    }
}
