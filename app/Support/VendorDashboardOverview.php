<?php

namespace App\Support;

use App\Models\User;

class VendorDashboardOverview
{
    public static function get(User $user): array
    {
        $account = $user->account_type();

        return [
            'products' => $user->myProducts()->where(['belongs_to_type' => $account])->count(),
            'sales' => $user->orderToMe()->where([
                'belongs_to_type' => $account,
                'status' => 'Confirm',
            ])->sum('total'),
        ];
    }
}