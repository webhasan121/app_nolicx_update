<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WithdrawIndexController extends Controller
{
    public function index()
    {
        $withdraw = auth()->user()->myWithdraw->map(function ($wtd) {
            return [
                'id' => $wtd->id,
                'status' => $wtd->status,
                'is_rejected' => $wtd->is_rejected,
                'amount' => $wtd->amount,
                'pay_by' => $wtd->pay_by,
                'pay_to' => $wtd->pay_to,
                'created_at' => $wtd->created_at?->toFormattedDateString(),
                'created_at_human' => $wtd->created_at?->diffForHumans(),
            ];
        });

        return Inertia::render('User/Wallet/Withdraw/Index', [
            'available_balance' => auth()->user()->abailCoin(),
            'withdraw' => $withdraw,
        ]);
    }
}
