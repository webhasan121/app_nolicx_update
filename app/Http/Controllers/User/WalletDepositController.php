<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\userDeposit;
use App\Support\SystemSettings;
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
            ->whereDate('created_at', today())
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

        return Inertia::render('User/Wallet/Deposit/History', [
            'coin' => $user->coin,
            'history' => $history,
            'payNumbers' => $this->depositPayNumbers(),
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

    private function depositPayNumbers(): array
    {
        $decoded = json_decode(SystemSettings::get('DEPOSIT_PAY_NUMBERS', '[]'), true);

        if (!is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->map(fn ($item) => [
                'name' => trim((string) ($item['name'] ?? '')),
                'value' => trim((string) ($item['value'] ?? '')),
            ])
            ->filter(fn ($item) => $item['name'] !== '' && $item['value'] !== '')
            ->values()
            ->all();
    }
}
