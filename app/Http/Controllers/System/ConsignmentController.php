<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\cod;
use App\Support\TableDateFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ConsignmentController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $type = $request->query('type', 'Pending');
        $find = $request->query('find');
        $sdate = $request->query('sdate');
        $edate = $request->query('edate');
        $defaultToday = TableDateFilter::hasOnlyDefaultFilters($request, ['type' => 'Pending']);

        $query = cod::query()->with('rider');

        if ($type !== 'All') {
            $query->where(['status' => $type]);
        }

        if (!empty($find)) {
            $query->where(function ($builder) use ($find) {
                $builder
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('order_id', 'like', '%' . $find . '%')
                    ->orWhereHas('rider', function ($riderQuery) use ($find) {
                        $riderQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%')
                            ->orWhere('phone', 'like', '%' . $find . '%');
                    });
            });
        }

        $this->applyDateFilter($query, $sdate, $edate, $defaultToday);

        $cod = $query->orderBy('id', 'desc')->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Auth/system/consignment/index', [
            'filters' => [
                'type' => $type,
                'find' => $find,
                'sdate' => $sdate,
                'edate' => $edate,
            ],
            'widgets' => [
                ['title' => 'Completed', 'value' => $this->status('Completed')],
                ['title' => 'Pending', 'value' => $this->status('Pending')],
                ['title' => 'Received', 'value' => $this->status('Received')],
                ['title' => 'Returned', 'value' => $this->status('Returned')],
            ],
            'cod' => [
                'data' => $cod->getCollection()->map(function (cod $item) {
                    return [
                        'id' => $item->id,
                        'order_id' => $item->order_id,
                        'rider_name' => $item->rider?->name,
                        'amount' => $item->amount,
                        'rider_amount' => $item->rider_amount,
                        'total_amount' => $item->total_amount,
                        'system_comission' => $item->system_comission,
                        'comission' => $item->comission,
                        'status' => $item->status,
                        'created_at_formatted' => Carbon::parse($item->created_at)->format('Y-M-d'),
                    ];
                })->values()->all(),
                'links' => collect($cod->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $cod->firstItem(),
                'to' => $cod->lastItem(),
                'total' => $cod->total(),
                'summary' => [
                    'count' => count($cod),
                    'amount' => $cod->getCollection()->sum('amount'),
                    'rider_amount' => $cod->getCollection()->sum('rider_amount'),
                    'total_amount' => $cod->getCollection()->sum('total_amount'),
                    'system_comission' => $cod->getCollection()->sum('system_comission'),
                    'comission' => $cod->getCollection()->sum('comission'),
                ],
            ],
            'printUrl' => route('system.consignment.print-summery', [
                'type' => $type,
                'find' => $find,
                'sdate' => $sdate,
                'edate' => $edate,
            ]),
        ]);
    }

    public function printReact(Request $request): Response
    {
        $type = $request->query('type', 'Pending');
        $find = $request->query('find');
        $sdate = $request->query('sdate');
        $edate = $request->query('edate');
        $defaultToday = TableDateFilter::hasOnlyDefaultFilters($request, ['type' => 'Pending']);

        $query = cod::query()->with('rider');

        if ($type !== 'All') {
            $query->where(['status' => $type]);
        }

        if (!empty($find)) {
            $query->where(function ($builder) use ($find) {
                $builder
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('order_id', 'like', '%' . $find . '%')
                    ->orWhereHas('rider', function ($riderQuery) use ($find) {
                        $riderQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%')
                            ->orWhere('phone', 'like', '%' . $find . '%');
                    });
            });
        }

        $this->applyDateFilter($query, $sdate, $edate, $defaultToday);

        $cod = $query->orderBy('id', 'desc')->get()->map(function (cod $item) {
            return [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'rider_name' => $item->rider?->name,
                'amount' => $item->amount,
                'rider_amount' => $item->rider_amount,
                'total_amount' => $item->total_amount,
                'system_comission' => $item->system_comission,
                'comission' => $item->comission,
                'status' => $item->status,
                'created_at_formatted' => Carbon::parse($item->created_at)->format('Y-M-d'),
            ];
        })->values()->all();

        return Inertia::render('Auth/system/consignment/PrintSummery', [
            'filters' => [
                'type' => $type,
                'find' => $find,
                'sdate' => $sdate,
                'edate' => $edate,
            ],
            'cod' => $cod,
            'summary' => [
                'count' => count($cod),
                'amount' => collect($cod)->sum('amount'),
                'rider_amount' => collect($cod)->sum('rider_amount'),
                'total_amount' => collect($cod)->sum('total_amount'),
                'system_comission' => collect($cod)->sum('system_comission'),
                'comission' => collect($cod)->sum('comission'),
            ],
        ]);
    }

    private function status(string $status = 'Pending'): int
    {
        return cod::query()->where('status', $status)->count();
    }

    private function applyDateFilter($query, ?string $sdate, ?string $edate, bool $defaultToday = false): void
    {
        TableDateFilter::apply($query, $sdate, $edate, $defaultToday);
    }
}
