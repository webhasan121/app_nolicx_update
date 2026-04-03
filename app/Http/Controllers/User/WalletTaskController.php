<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\user_task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletTaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = user_task::where(['user_id' => $request->user()->id])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                $m = round($item->time / 60, 0);
                $s = $item->time % 60;

                return [
                    'id' => $item->id,
                    'date' => Carbon::parse($item->created_at)->toFormattedDateString(),
                    'earning' => $item->coin ?? 0,
                    'time' => $m . ' : ' . $s . ' min',
                ];
            });

        return Inertia::render('User/Wallet/Task', [
            'tasks' => $tasks,
        ]);
    }
}

