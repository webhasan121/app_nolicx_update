<?php

namespace App\Support;

use App\Models\Packages;
use App\Models\User;
use App\Models\UserHasRefs;
use App\Models\Vip;
use Illuminate\Support\Facades\DB;

class VipReferralCommission
{
    public static function purchaseData(array $data, User $user, Packages $package): array
    {
        $ref = self::refForUser($user);

        $data['reference'] = $user->reference ?: config('app.ref');
        $data['comission'] = $package->ref_owner_get_coin ?? 0;

        if ($ref) {
            $data['reference'] = $ref->ref;
            $data['refer'] = $ref->user_id;
        }

        return $data;
    }

    public static function ensureVipReferral(Vip $vip): Vip
    {
        if ($vip->refer) {
            return $vip;
        }

        $vip->loadMissing(['user', 'package']);
        $ref = self::refForUser($vip->user);

        if (!$ref) {
            return $vip;
        }

        $vip->reference = $ref->ref;
        $vip->refer = $ref->user_id;
        $vip->comission = $vip->package?->ref_owner_get_coin ?? $vip->comission ?? 0;
        $vip->save();

        return $vip;
    }

    public static function award(Vip $vip): bool
    {
        $vip = self::ensureVipReferral($vip);

        if (!$vip->refer) {
            return false;
        }

        $amount = (float) ($vip->comission ?? $vip->package?->ref_owner_get_coin ?? 0);

        if ($amount <= 0) {
            return false;
        }

        DB::transaction(function () use ($vip, $amount) {
            User::query()
                ->whereKey($vip->refer)
                ->lockForUpdate()
                ->first()
                ?->increment('coin', $amount);
        });

        return true;
    }

    private static function refForUser(?User $user): ?UserHasRefs
    {
        if (!$user?->reference || $user->reference === config('app.ref')) {
            return null;
        }

        $ref = UserHasRefs::query()
            ->where('ref', $user->reference)
            ->first();

        if (!$ref || (int) $ref->user_id === (int) $user->id) {
            return null;
        }

        return $ref;
    }
}
