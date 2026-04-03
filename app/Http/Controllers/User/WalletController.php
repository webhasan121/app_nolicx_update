<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DistributeComissions;
use App\Models\TakeComissions;
use App\Models\user_task;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function index()
    {
        $withdraw = Withdraw::where(['user_id' => Auth::id(), 'status' => 'Pending'])->latest('id')->get()->map(function ($cus) {
            return [
                'id' => $cus->id,
                'amount' => $cus->amount,
                'status' => $cus->status,

                'created_at' => $cus->created_at?->toFormattedDateString(),
                'created_at_human' => $cus->created_at?->diffForHumans(),
            ];
        });
        $comission = DistributeComissions::where(['user_id' => Auth::id(), 'confirmed' => true])->whereDate('updated_at', today())->sum('amount');
        $cut = TakeComissions::where(['user_id' => Auth::id(), 'confirmed' => true])->whereDate('updated_at', today())->sum('take_comission');
        $reffer = auth()->user()->getMyvipRef()->whereDate('updated_at', today())->sum('comission');
        $task = user_task::where(['user_id' => Auth::id()])->whereDate('updated_at', '=', today())->first();

        return Inertia::render('User/Wallet/Index', [
            'task' => $task,
            'comission' => $comission,
            'cut' => $cut,
            'reffer' => $reffer,
            'withdraw' => $withdraw,
            'available_balance' => auth()->user()->abailCoin(),
        ]);
    }
}
