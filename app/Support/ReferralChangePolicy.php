<?php

namespace App\Support;

use App\Models\User;
use App\Models\Vip;

class ReferralChangePolicy
{
    public static function status(User $user): array
    {
        $hasVip = Vip::query()->where('user_id', $user->id)->exists();
        $withinTime = $user->created_at?->diffInHours(now()) <= 72;

        if ($hasVip) {
            return [
                'can_apply' => false,
                'status' => 'applied',
                'message' => 'Applied',
            ];
        }

        if (!$withinTime) {
            return [
                'can_apply' => false,
                'status' => 'expired',
                'message' => 'Time Up',
            ];
        }

        return [
            'can_apply' => true,
            'status' => 'open',
            'message' => null,
        ];
    }

    public static function markApplied(User $user): void
    {
        $user->reference_accepted_at = now();
    }
}
