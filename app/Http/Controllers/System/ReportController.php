<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\userDeposit;
use App\Models\Withdraw;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function indexReact(Request $request): Response
    {
        return Inertia::render('Auth/system/report/index', [
            'filters' => [
                'nav' => $request->query('nav', 'Deposit'),
                'sdate' => $request->query('sdate', ''),
                'edate' => $request->query('edate', ''),
                'sid' => $request->query('sid', ''),
            ],
        ]);
    }

    public function generateReact(Request $request): Response
    {
        $nav = $request->query('nav', 'Deposit');
        $sdate = $request->query('sdate', today()->format('Y-m-d'));
        $edate = $request->query('edate', today()->format('Y-m-d'));

        $payload = [
            'nav' => $nav,
        ];

        if ($nav === 'Deposit') {
            $history = userDeposit::query()
                ->with('user')
                ->where(['confirmed' => false])
                ->whereBetween('created_at', [$sdate, Carbon::parse($edate)->endOfDay()])
                ->orderBy('id', 'desc')
                ->paginate((int) config('app.config'))
                ->withQueryString();

            $payload['deposit'] = [
                'sdate' => Carbon::parse($sdate)->format('d/m/Y'),
                'edate' => Carbon::parse($edate)->format('d/m/Y'),
                'history' => [
                    'data' => $history->getCollection()->map(function (userDeposit $item) {
                        return [
                            'id' => $item->id,
                            'user' => [
                                'id' => $item->user?->id ?? 0,
                                'name' => $item->user?->name ?? 'N/A',
                            ],
                            'amount' => $item->amount ?? 0,
                            'senderAccountNumber' => $item->senderAccountNumber,
                            'paymentMethod' => $item->paymentMethod,
                            'receiverAccountNumber' => $item->receiverAccountNumber,
                            'transactionId' => $item->transactionId ?? 'N/A',
                            'confirmed' => (bool) $item->confirmed,
                            'created_at_diff' => $item->created_at?->diffForHumans(),
                        ];
                    })->values()->all(),
                    'sum' => (float) $history->getCollection()->sum('amount'),
                ],
            ];
        }

        if ($nav === 'Withdraw') {
            $withdraws = Withdraw::query()
                ->with('user')
                ->where(['status' => 1])
                ->whereBetween('created_at', [$sdate, Carbon::parse($edate)->endOfDay()])
                ->orderBy('id', 'desc')
                ->paginate((int) config('app.pagination'))
                ->withQueryString();

            $payload['withdraw'] = [
                'filters' => [
                    'sdate_formatted' => Carbon::parse($sdate)->format('d/m/Y'),
                    'edate_formatted' => Carbon::parse($edate)->format('d/m/Y'),
                ],
                'withdraws' => $withdraws->getCollection()->map(function (Withdraw $item) {
                    return [
                        'id' => $item->id,
                        'seen_by_admin' => (bool) $item->seen_by_admin,
                        'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                        'amount' => $item->amount ?? 0,
                        'total_fee' => $item->total_fee ?? 0,
                        'payable_amount' => $item->payable_amount ?? 0,
                        'status' => (bool) $item->status,
                        'is_rejected' => (bool) $item->is_rejected,
                        'user' => [
                            'name' => $item->user?->name,
                            'email' => $item->user?->email,
                            'subscription' => (bool) $item->user?->subscription,
                        ],
                    ];
                })->values()->all(),
                'summary' => [
                    'sum_amount' => $withdraws->getCollection()->sum('amount'),
                    'sum_total_fee' => $withdraws->getCollection()->sum('total_fee'),
                    'sum_payable_amount' => $withdraws->getCollection()->sum('payable_amount'),
                ],
            ];
        }

        return Inertia::render('Auth/system/report/Report', $payload);
    }
}
