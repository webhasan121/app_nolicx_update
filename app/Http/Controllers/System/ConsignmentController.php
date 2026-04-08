<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\cod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ConsignmentController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $type = $request->query('type', 'Pending');
        $sdate = $request->query('sdate', now()->format('Y-m-d'));
        $edate = $request->query('edate', now()->format('Y-m-d'));

        $query = cod::query()->with('rider');

        if ($type !== 'All') {
            $query->where(['status' => $type]);
        }

        $query->whereBetween('created_at', [$sdate, Carbon::parse($edate)->endOfDay()]);

        $cod = $query->orderBy('id', 'desc')->paginate(30)->withQueryString();

        return Inertia::render('Auth/system/consignment/index', [
            'filters' => [
                'type' => $type,
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
                'links' => $cod->linkCollection()->toArray(),
                'summary' => [
                    'count' => count($cod),
                    'amount' => $cod->getCollection()->sum('amount'),
                    'rider_amount' => $cod->getCollection()->sum('rider_amount'),
                    'total_amount' => $cod->getCollection()->sum('total_amount'),
                    'system_comission' => $cod->getCollection()->sum('system_comission'),
                    'comission' => $cod->getCollection()->sum('comission'),
                ],
            ],
        ]);
    }

    private function status(string $status = 'Pending'): int
    {
        return cod::query()->where('status', $status)->count();
    }
}
