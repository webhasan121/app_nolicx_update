<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletRefferController extends Controller
{
    public function index(Request $request)
    {
        $refs = $request->user()->getMyvipRef()
            ->with('user')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'comission' => $item->comission ?? 0,
                    'user' => $item->user?->name ?? 'N/A',
                    'date' => $item->created_at ? Carbon::parse($item->created_at)->diffForHumans() : 'N/A',
                ];
            });

        return Inertia::render('User/Wallet/Reffer', [
            'refs' => $refs,
        ]);
    }
}

