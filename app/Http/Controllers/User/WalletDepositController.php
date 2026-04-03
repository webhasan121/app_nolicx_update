<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Package_pays;
use App\Models\Packages;
use App\Models\userDeposit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletDepositController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole(['reseller', 'vendor', 'rider', 'system'])) {
            return redirect()->route('dashboard');
        }

        $history = $user->myDeposit()
            ->latest('id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'amount' => $item->amount ?? 0,
                    'paymentMethod' => $item->paymentMethod,
                    'receiverAccountNumber' => $item->receiverAccountNumber,
                    'senderAccountNumber' => $item->senderAccountNumber,
                    'transactionId' => $item->transactionId ?? 'N/A',
                    'confirmed' => (bool) $item->confirmed,
                    'date' => $item->created_at ? Carbon::parse($item->created_at)->diffForHumans() : 'N/A',
                ];
            });

        $packageId = Packages::query()->value('id');
        $payNumbers = $packageId
            ? Package_pays::where(['package_id' => $packageId])->pluck('pay_to', 'pay_type')
            : collect();

        return Inertia::render('User/Wallet/Deposit/History', [
            'coin' => $user->coin,
            'history' => $history,
            'payNumbers' => $payNumbers,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole(['reseller', 'vendor', 'rider', 'system'])) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'amount' => ['required'],
            'paymentMethod' => ['required'],
            'receiverAccountNumber' => ['required'],
            'senderName' => ['required'],
            'senderAccountNumber' => ['required'],
            'transactionId' => ['required'],
        ]);

        $deposit = new userDeposit();
        $deposit->forceFill([
            'amount' => $validated['amount'],
            'paymentMethod' => $validated['paymentMethod'],
            'receiverAccountNumber' => $validated['receiverAccountNumber'],
            'senderName' => $validated['senderName'],
            'senderAccountNumber' => $validated['senderAccountNumber'],
            'transactionId' => $validated['transactionId'],
            'user_id' => $user->id,
            'confirmed' => false,
        ]);
        $deposit->save();

        return redirect()->back()->with('success', 'Deposit has been requested !');
    }
}

