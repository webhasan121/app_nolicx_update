<?php

namespace App\Models;

use App\Http\Controllers\UserWalletController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class DistributeComissions extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'user_id',
        'store_id',
        'product_id ',
        'order_id',
        'parent_id',
        'confirmed',
    ];

    protected static function booted(): void
    {
        // parent::boot();
        static::created(function ($distributeComissions) {
            if ($distributeComissions->confirmed == true) {
                $distributeComissions->creditWalletOnce();
            }
        });

        static::updated(function ($distributeComissions) {
            $takeCom = $distributeComissions->take;


            if ($distributeComissions->isDirty('confirmed')) {
                if ($distributeComissions->confirmed == true) {
                    $distributeComissions->creditWalletOnce();
                } elseif ($distributeComissions->confirmed == false) {
                    $distributeComissions->uncreditWalletOnce();
                }
            }

            // if (DistributeComissions::where(['parent_id' => $distributeComissions->parent_id])->confirmed()->count() == 0) {
            //     $takeCom->confirmed = false;
            //     $takeCom->save();
            // }
        });
    }


    // cast 
    protected $casts = [
        'confirmed' => 'boolean',
        'amount' => 'float',
        'take_comission' => 'float',
        'distribute_comission' => 'float',
        'store' => 'float',
        'return' => 'float',
        'profit' => 'float',
        'wallet_credited_at' => 'datetime',
    ];

    private static ?bool $hasWalletCreditColumn = null;

    public static function hasWalletCreditColumn(): bool
    {
        if (self::$hasWalletCreditColumn === null) {
            self::$hasWalletCreditColumn = Schema::hasTable('distribute_comissions')
                && Schema::hasColumn('distribute_comissions', 'wallet_credited_at');
        }

        return self::$hasWalletCreditColumn;
    }

    public static function creditPendingWalletsForConfirmed(int $limit = 500): int
    {
        if (!self::hasWalletCreditColumn()) {
            return 0;
        }

        $credited = 0;

        self::query()
            ->confirmed()
            ->whereNull('wallet_credited_at')
            ->where('amount', '>', 0)
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->each(function (self $commission) use (&$credited) {
                if ($commission->creditWalletOnce()) {
                    $credited++;
                }
            });

        return $credited;
    }

    public function creditWalletOnce(): bool
    {
        if (!$this->user_id || (float) $this->amount <= 0) {
            return false;
        }

        if (self::hasWalletCreditColumn() && $this->wallet_credited_at) {
            return false;
        }

        $result = UserWalletController::add($this->user_id, $this->amount);

        if (!($result['success'] ?? false)) {
            return false;
        }

        if (self::hasWalletCreditColumn()) {
            $this->forceFill(['wallet_credited_at' => now()])->saveQuietly();
        }

        return true;
    }

    public function uncreditWalletOnce(): bool
    {
        if (!$this->user_id || (float) $this->amount <= 0) {
            return false;
        }

        if (self::hasWalletCreditColumn() && !$this->wallet_credited_at) {
            return false;
        }

        $result = UserWalletController::remove($this->user_id, $this->amount);

        if (!($result['success'] ?? false)) {
            return false;
        }

        if (self::hasWalletCreditColumn()) {
            $this->forceFill(['wallet_credited_at' => null])->saveQuietly();
        }

        return true;
    }



    /**
     * scope
     */

    public function scopePending($query)
    {
        return $query->where(['confirmed' => false]);
    }

    public function scopeConfirmed($query)
    {
        return $query->where(['confirmed' => 1]);
    }


    /**
     * relationship
     */
    public function take()
    {
        return $this->belongsTo(TakeComissions::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storeInfo() {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
}
