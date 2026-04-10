<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\TakeComissions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ComissionsController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'where' => $request->query('where', ''),
            'wid' => $request->query('wid', ''),
            'confirm' => $request->query('confirm', 'All'),
            'from' => $request->query('from', now()->toDateString()),
            'to' => $request->query('to', now()->toDateString()),
        ];

        $query = TakeComissions::query();

        if ($filters['confirm'] !== 'All' && $filters['confirm'] !== '') {
            $query->where('confirmed', $filters['confirm'] === 'true' ? 1 : 0);
        }

        $query
            ->when($filters['where'] === 'user_id', fn($q) => $q->where('user_id', $filters['wid']))
            ->when($filters['where'] === 'product_id', fn($q) => $q->where('product_id', $filters['wid']))
            ->when($filters['where'] === 'order_id', fn($q) => $q->where('order_id', $filters['wid']))
            ->when($filters['from'], fn($q) => $q->whereDate('created_at', '>=', $filters['from']))
            ->when($filters['to'], fn($q) => $q->whereDate('created_at', '<=', $filters['to']))
            ->when($filters['wid'] && $filters['where'] === '', fn($q) => $q->where('id', $filters['wid']));

        $comissions = $query->get();

        return Inertia::render('Reseller/Comissions/Index', [
            'filters' => [
                ...$filters,
                'from_formatted' => $filters['from'] ? Carbon::parse($filters['from'])->format('d/m/Y') : '',
                'to_formatted' => $filters['to'] ? Carbon::parse($filters['to'])->format('d/m/Y') : '',
            ],
            'comissions' => $comissions->map(fn(TakeComissions $item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'order_id' => $item->order_id ?? 0,
                'product_id' => $item->product_id ?? 0,
                'buying_price' => $item->buying_price ?? 0,
                'selling_price' => $item->selling_price ?? 0,
                'profit' => $item->profit ?? 0,
                'comission_range' => $item->comission_range ?? 0,
                'take_comission' => $item->take_comission ?? 0,
                'distribute_comission' => $item->distribute_comission ?? 0,
                'store' => $item->store ?? 0,
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                'confirmed' => (bool) $item->confirmed,
            ])->values()->all(),
        ]);
    }
}
