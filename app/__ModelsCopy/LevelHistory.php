<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LevelHistory extends Model {
    use SoftDeletes;

    protected $fillable = [ 'user_id', 'from_level_id', 'to_level_id' ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function fromLevel() {
        return $this->belongsTo(Level::class, 'from_level_id');
    }

    public function toLevel() {
        return $this->belongsTo(Level::class, 'to_level_id');
    }

}
