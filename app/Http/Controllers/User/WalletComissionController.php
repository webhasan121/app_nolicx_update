<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DistributeComissions;
use App\Models\ResellerResellProfits;
use App\Models\TakeComissions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletComissionController extends Controller
{
    public function index(Request $request)
    {
        $nav = $request->query('nav', 'earn');
        $set = $request->query('set', 'com');

        if ($nav === 'system') {
            $data = TakeComissions::with('product')
                ->where(['user_id' => $request->user()->id, 'confirmed' => true])
                ->paginate(config('app.paginate'));
        } elseif ($set === 'prof') {
            $data = ResellerResellProfits::with('product')
                ->where(['to' => $request->user()->id, 'confirmed' => true])
                ->paginate(config('app.paginate'));
        } else {
            $data = DistributeComissions::with('product')
                ->where(['user_id' => $request->user()->id, 'confirmed' => true])
                ->paginate(config('app.paginate'));
        }

        $rows = collect($data->items())->map(function ($item) use ($nav, $set) {
            if ($set === 'prof') {
                return [
                    'id' => $item->id,
                    'product' => $item->product?->name ?? 0,
                    'profit' => $item->profit ?? 0,
                    'date' => Carbon::parse($item->updated_at)->toFormattedDateString(),
                ];
            }

            if ($nav === 'system') {
                return [
                    'id' => $item->id,
                    'amount' => $item->take_comission,
                    'product' => $item->product?->name ?? 'N/A',
                    'order' => $item->order_id ?? 'N/A',
                    'date' => Carbon::parse($item->updated_at)->toFormattedDateString(),
                ];
            }

            return [
                'id' => $item->id,
                'product' => $item->product?->name ?? 0,
                'amount' => $item->amount ?? 0,
                'date' => Carbon::parse($item->updated_at)->toFormattedDateString(),
            ];
        })->values();

        return Inertia::render('User/Wallet/Comission', [
            'nav' => $nav,
            'set' => $set,
            'rows' => $rows,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'links' => $data->linkCollection(),
            ],
        ]);
    }
}

