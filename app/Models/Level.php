<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Level extends Model {
    use SoftDeletes;

    protected $fillable = [ 'name', 'slug', 'req_users', 'vip_users', 'bonus', 'rewards', 'status' ];

    // Automatically generate slug
    protected static function booted() {
        // On creating, always generate slug
        static::creating(function ($level) {
            $level->slug = Str::slug($level->name);
        });

        // On updating, only generate slug if name changed
        static::updating(function ($level) {
            if ($level->isDirty('name')) {
                $level->slug = Str::slug($level->name);
            }
        });
    }

    public function userLevel() {
        return $this->hasMany(User::class, 'current_level_id', 'id')->withTrashed();
    }

    public function levelUpsFrom() {
        return $this->hasMany(LevelHistory::class, 'from_level_id');
    }

    public function levelUpsTo() {
        return $this->hasMany(LevelHistory::class, 'to_level_id');
    }
}
