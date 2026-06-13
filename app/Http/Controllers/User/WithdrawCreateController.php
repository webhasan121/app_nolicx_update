<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WithdrawCreateController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessAccounts = ['rider', 'reseller', 'vendor'];

        return Inertia::render('User/Wallet/Withdraw/Create', [
            'wallet_balance' => $user->coin ?? 0,
            'available_balance' => $user->abailCoin(),
            'phone' => $user->phone,
            'minimum_remaining_balance' => in_array($user->active_nav, $businessAccounts, true) ? 200 : 0,
            'minimum_remaining_balance_applies' => in_array($user->active_nav, $businessAccounts, true),
        ]);
    }
}
