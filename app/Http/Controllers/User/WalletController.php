<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DistributeComissions;
use App\Models\TakeComissions;
use App\Models\user_task;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $find = trim((string) $request->query('find', ''));
        $query = Withdraw::query()
            ->where(['user_id' => Auth::id(), 'status' => 'Pending'])
            ->latest('id');

        if ($find !== '') {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('amount', 'like', '%' . $find . '%')
                    ->orWhere('pay_by', 'like', '%' . $find . '%')
                    ->orWhere('pay_to', 'like', '%' . $find . '%');
            });
        }

        $withdraw = $query->paginate(config('app.paginate'))->withQueryString();
        $comission = DistributeComissions::where(['user_id' => Auth::id(), 'confirmed' => true])->whereDate('updated_at', today())->sum('amount');
        $cut = TakeComissions::where(['user_id' => Auth::id(), 'confirmed' => true])->whereDate('updated_at', today())->sum('take_comission');
        $reffer = auth()->user()->getMyvipRef()->whereDate('updated_at', today())->sum('comission');
        $task = user_task::where(['user_id' => Auth::id()])->whereDate('updated_at', '=', today())->first();

        return Inertia::render('User/Wallet/Index', [
            'task' => $task,
            'comission' => $comission,
            'cut' => $cut,
            'reffer' => $reffer,
            'filters' => [
                'find' => $find,
            ],
            'withdraw' => [
                'data' => $withdraw->getCollection()->map(function ($cus) {
                    return [
                        'id' => $cus->id,
                        'amount' => $cus->amount,
                        'status' => $cus->status,
                        'created_at' => $cus->created_at?->toFormattedDateString(),
                        'created_at_human' => $cus->created_at?->diffForHumans(),
                    ];
                })->values()->all(),
                'links' => collect($withdraw->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $withdraw->firstItem(),
                'to' => $withdraw->lastItem(),
                'total' => $withdraw->total(),
            ],
            'printUrl' => route('user.wallet.print', [
                'find' => $find,
            ]),
            'available_balance' => auth()->user()->abailCoin(),
        ]);
    }

    public function print(Request $request)
    {
        $find = trim((string) $request->query('find', ''));
        $query = Withdraw::query()
            ->where(['user_id' => Auth::id(), 'status' => 'Pending'])
            ->latest('id');

        if ($find !== '') {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('amount', 'like', '%' . $find . '%')
                    ->orWhere('pay_by', 'like', '%' . $find . '%')
                    ->orWhere('pay_to', 'like', '%' . $find . '%');
            });
        }

        $withdraw = $query->get()->map(function ($cus) {
            return [
                'id' => $cus->id,
                'amount' => $cus->amount,
                'status' => $cus->status,
                'created_at' => $cus->created_at?->toFormattedDateString(),
                'created_at_human' => $cus->created_at?->diffForHumans(),
            ];
        })->values()->all();

        return Inertia::render('User/Wallet/Print', [
            'filters' => [
                'find' => $find,
            ],
            'withdraw' => $withdraw,
            'available_balance' => auth()->user()->abailCoin(),
        ]);
    }
}
