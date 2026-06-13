<?php

namespace App\Models;

use App\Events\NoticeBroadcasted;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'target_user_id',
        'title',
        'body',
        'order_id',
        'target_roles',
        'is_active',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'target_roles' => 'array',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function reads()
    {
        return $this->hasMany(NoticeRead::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder) {
                $builder
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function (Builder $builder) {
                $builder
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeForRoles(Builder $query, array $roles): Builder
    {
        return $query->where(function (Builder $builder) use ($roles) {
            $builder->whereNull('target_roles');

            foreach ($roles as $role) {
                $builder->orWhereJsonContains('target_roles', $role);
            }
        });
    }

    protected static function booted(): void
    {
        static::created(fn (Notice $notice) => NoticeBroadcasted::dispatch($notice->loadMissing('creator:id,name'), 'created'));
        static::updated(fn (Notice $notice) => NoticeBroadcasted::dispatch($notice->loadMissing('creator:id,name'), 'updated'));
        static::deleted(fn (Notice $notice) => NoticeBroadcasted::dispatch($notice, 'deleted'));
    }
}
