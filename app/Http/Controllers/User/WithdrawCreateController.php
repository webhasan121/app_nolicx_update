<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WithdrawCreateController extends Controller
{
    public function index()
    {
        return Inertia::render('User/Wallet/Withdraw/Create', [
            'available_balance' => auth()->user()->abailCoin(),
            'phone' => auth()->user()->phone,
        ]);
    }
}

