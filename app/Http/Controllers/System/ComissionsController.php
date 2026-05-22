<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\TakeComissions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ComissionsController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $confirm = $request->query('confirm', 'All');
        $where = $request->query('where', '');
        $wid = trim((string) $request->query('wid', ''));
        $from = $request->query('from');
        $to = $request->query('to');

        $comissions = $this->queryResult($confirm, $where, $wid, $from, $to)
            ->latest('id')
            ->paginate((int) config('app.paginate'))
            ->withQueryString();

        return Inertia::render('Auth/system/comissions/index', [
            'filters' => [
                'confirm' => $confirm,
                'where' => $where,
                'wid' => $wid,
                'from' => $from,
                'to' => $to,
            ],
            'comissions' => [
                'data' => $comissions->getCollection()->map(fn (TakeComissions $item) => [
                    'id' => $item->id,
                    'created_at_formatted' => $item->created_at?->format('d M Y'),
                    'order_id' => $item->order_id ?? 0,
                    'product_id' => $item->product_id ?? 0,
                    'buying_price' => $this->money($item->buying_price),
                    'selling_price' => $this->money($item->selling_price),
                    'profit' => $this->money($item->profit),
                    'comission_range' => $this->money($item->comission_range),
                    'take_comission' => $this->money($item->take_comission),
                    'distribute_comission' => $this->money($item->distribute_comission),
                    'store' => $this->money($item->store),
                    'return' => $this->money($item->return),
                    'confirmed' => (bool) $item->confirmed,
                ])->values()->all(),
                'links' => collect($comissions->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $comissions->firstItem(),
                'to' => $comissions->lastItem(),
                'total' => $comissions->total(),
                'summary' => [
                    'profit' => $this->money($comissions->getCollection()->sum('profit')),
                    'take_comission' => $this->money($comissions->getCollection()->sum('take_comission')),
                    'distribute_comission' => $this->money($comissions->getCollection()->sum('distribute_comission')),
                    'store' => $this->money($comissions->getCollection()->sum('store')),
                    'return' => $this->money($comissions->getCollection()->sum('return')),
                    'buying_price' => $this->money($comissions->getCollection()->sum('buying_price')),
                    'selling_price' => $this->money($comissions->getCollection()->sum('selling_price')),
                ],
            ],
        ]);
    }

    public function takesReact(Request $request): Response
    {
        $confirm = $request->query('confirm', 'All');
        $where = $request->query('where', '');
        $wid = trim((string) $request->query('wid', ''));
        $from = $request->query('from');
        $to = $request->query('to');

        $comissions = $this->queryResult($confirm, $where, $wid, $from, $to)
            ->get();

        return Inertia::render('Auth/system/comissions/Takes', [
            'filters' => [
                'confirm' => $confirm,
                'where' => $where,
                'wid' => $wid,
                'from' => $from,
                'to' => $to,
                'from_formatted' => $from ? Carbon::parse($from)->format('d/m/Y') : '',
                'to_formatted' => $to ? Carbon::parse($to)->format('d/m/Y') : '',
            ],
            'comissions' => $comissions->map(fn (TakeComissions $item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'order_id' => $item->order_id ?? 0,
                'product_id' => $item->product_id ?? 0,
                'buying_price' => $this->money($item->buying_price),
                'selling_price' => $this->money($item->selling_price),
                'profit' => $this->money($item->profit),
                'comission_range' => $this->money($item->comission_range),
                'take_comission' => $this->money($item->take_comission),
                'distribute_comission' => $this->money($item->distribute_comission),
                'store' => $this->money($item->store),
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                'confirmed' => (bool) $item->confirmed,
            ])->values()->all(),
        ]);
    }

    public function detailsReact(int $id): Response
    {
        $data = TakeComissions::query()->where('id', $id)->get();

        return Inertia::render('Auth/system/comissions/Details', [
            'data' => $data->map(fn (TakeComissions $item) => [
                'id' => $item->id,
                'order_id' => $item->order_id ?? 0,
                'product_id' => $item->product_id ?? 0,
                'buying_price' => $this->money($item->buying_price),
                'selling_price' => $this->money($item->selling_price),
                'profit' => $this->money($item->profit),
                'comission_range' => $this->money($item->comission_range),
                'take_comission' => $this->money($item->take_comission),
                'distribute_comission' => $this->money($item->distribute_comission),
                'store' => $this->money($item->store),
                'return' => $this->money($item->return),
                'confirmed' => (bool) $item->confirmed,
            ])->values()->all(),
        ]);
    }

    public function distributesReact(int $id): Response
    {
        $takes = TakeComissions::query()->with('product', 'user')->findOrFail($id);
        $distributes = \App\Models\DistributeComissions::query()
            ->with('product', 'user')
            ->where(['parent_id' => $id])
            ->get();

        return Inertia::render('Auth/system/comissions/Distributes', [
            'takes' => [
                'id' => $takes->id,
                'user_id' => $takes->user_id,
                'order_id' => $takes->order_id ?? 0,
                'product_id' => $takes->product_id ?? 0,
                'product_name' => $takes->product?->name ?? 0,
                'product_thumbnail' => $takes->product?->thumbnail,
                'buying_price' => $this->money($takes->buying_price),
                'selling_price' => $this->money($takes->selling_price),
                'profit' => $this->money($takes->profit),
                'comission_range' => $this->money($takes->comission_range),
                'take_comission' => $this->money($takes->take_comission),
                'distribute_comission' => $this->money($takes->distribute_comission),
                'store' => $this->money($takes->store),
                'return' => $this->money($takes->return),
            ],
            'distributes' => $distributes->map(fn ($item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'user_name' => $item->user?->name ?? 0,
                'product_name' => $item->product?->name ?? 0,
                'amount' => $this->money($item->amount),
                'range' => $this->money($item->range),
                'confirmed' => (bool) $item->confirmed,
            ])->values()->all(),
        ]);
    }

    private function queryResult($confirm, $where, $wid, $from, $to)
    {
        $q = TakeComissions::query();

        if ($confirm !== 'All' && $confirm !== null && $confirm !== '') {
            $q->where(['confirmed' => $confirm == 'true' ? 1 : 0]);
        }

        $q->when($where == 'user_id' && $wid !== '', function ($query) use ($wid) {
            return $query->where('user_id', $wid);
        })
            ->when($where == 'product_id' && $wid !== '', function ($query) use ($wid) {
                return $query->where('product_id', $wid);
            })
            ->when($where == 'order_id' && $wid !== '', function ($query) use ($wid) {
                return $query->where('order_id', $wid);
            })
            ->when($wid !== '' && $where == '', function ($query) use ($wid) {
                return $query->where(function ($subQuery) use ($wid) {
                    $subQuery
                        ->where('id', 'like', '%' . $wid . '%')
                        ->orWhere('user_id', 'like', '%' . $wid . '%')
                        ->orWhere('product_id', 'like', '%' . $wid . '%')
                        ->orWhere('order_id', 'like', '%' . $wid . '%')
                        ->orWhere('buying_price', 'like', '%' . $wid . '%')
                        ->orWhere('selling_price', 'like', '%' . $wid . '%')
                        ->orWhere('profit', 'like', '%' . $wid . '%');
                });
            });

        $this->applyDateFilter($q, $from, $to);

        return $q;
    }

    private function applyDateFilter($query, ?string $from, ?string $to): void
    {
        if (!empty($from) && !empty($to)) {
            $start = Carbon::parse($from)->startOfDay();
            $end = Carbon::parse($to)->endOfDay();

            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $query->whereBetween('created_at', [$start, $end]);

            return;
        }

        if (!empty($from)) {
            $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($from)->endOfDay(),
            ]);

            return;
        }

        if (!empty($to)) {
            $query->whereBetween('created_at', [
                Carbon::parse($to)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        }
    }

    private function money($value): float
    {
        return round((float) ($value ?? 0), 2);
    }
}
