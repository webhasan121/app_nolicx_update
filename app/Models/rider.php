<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class rider extends Model
{
    // guraded all fillable data
    protected $fillable = [
        'user_id',
        'phone',
        'email',

        'nid',
        'nid_photo_front',
        'nid_photo_back',

        'fixed_address',

        'current_address',

        'area_condition', // inside dhaka or outside
        'targeted_area',

        'fixed_amount',
        'comission',
        'is_rejected',
        'rejected_for',
        'doc_1',
        'doc_2',
        'doc_3',
        'doc_4',

        'country',
        'district',
        'city',
        'upozila',
        'village',

        'status',

        // rider vehicle info
        'vehicle_type', // e.g. Bike, Car
        'vehicle_number', // e.g. Dhaka Metro 1234
        'vehicle_model', // e.g. Yamaha YZF-R3
        'vehicle_color', // e.g. Red
    ];

    // hidden
    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    ////////////////
    // boot //
    ///////////////
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (rider $data) {
            $data->status = 'Pending';
        });

        static::updated(function (rider $rider) {
            /**
             * if the status field is updated,
             * and status is Active,
             * then assign the rider role
             */

            if (! $rider->wasChanged('status')) {
                return;
            }

            $riderRole = Role::where('name', 'rider')->first();
            if (! $riderRole) {
                return;
            }

            if ($rider->status == 'Active') {
                $rider->user?->assignRole($riderRole);
                return;
            }

            if ($rider->user?->hasRole($riderRole)) {
                $rider->user?->removeRole($riderRole);
            }
        });
    }

    ////////////////
    // attributes //
    ///////////////
    protected function country(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    protected function district(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    protected function current_address(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    protected function targeted_area(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    protected function upozila(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    protected function village(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    protected function vehicle_type(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    protected function vehicle_model(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    protected function vehicle_color(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value), // uppercase the world
        );
    }

    ////////////////
    // SCOPE //
    ///////////////

    public function scopeActive($query)
    {
        return $query->where(['status' => 'Active']);
    }

    public function scopePending($query)
    {
        return $query->where(['status' => 'Pending']);
    }
    public function scopeSuspended($query)
    {
        return $query->where(['status' => 'Suspended']);
    }
    public function scopeDisabled($query)
    {
        return $query->where(['status' => 'Disabled']);
    }

    public function scopeIsActive()
    {
        return $this->status == 'Active';
    }

    ////////////////
    // relation //
    ///////////////
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(
            [
                'id' => 0,
                'name' => 'Deleted Rider',
                'email' => 'not found',
            ]
        );
    }
    public function cod()
    {
        return $this->hasMany(cod::class, 'id', 'rider_id');
    }

    public function targetedArea() {
        return $this->belongsTo(ta::class, 'targeted_area', 'id');
    }
}
