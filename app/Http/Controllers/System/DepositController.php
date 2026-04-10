<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\userDeposit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DepositController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $status = (string) $request->query('status', '*');
        $find = trim((string) $request->query('find', ''));
        $sdate = $request->query('sdate');
        $edate = $request->query('edate');

        $history = $this->queryDeposits($status, $find, $sdate, $edate)
            ->orderBy('id', 'desc')
            ->paginate((int) config('app.paginate'))
            ->withQueryString();

        return Inertia::render('Auth/system/deposit/index', [
            'status' => $status,
            'find' => $find,
            'sdate' => $sdate,
            'edate' => $edate,
            'history' => [
                'data' => $history->getCollection()->map(fn (userDeposit $item) => $this->transformDeposit($item))->values()->all(),
                'links' => collect($history->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
                'per_page' => $history->perPage(),
                'total' => $history->total(),
                'from' => $history->firstItem(),
                'to' => $history->lastItem(),
                'sum' => (float) $history->getCollection()->sum('amount'),
            ],
        ]);
    }

    public function printReact(Request $request): Response
    {
        $status = (string) $request->query('status', '*');
        $find = trim((string) $request->query('find', ''));
        $sdate = $request->query('sdate');
        $edate = $request->query('edate');

        $history = $this->queryDeposits($status, $find, $sdate, $edate)
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Auth/system/deposit/PrintSummery', [
            'status' => $status,
            'find' => $find,
            'sdate' => $sdate,
            'edate' => $edate,
            'history' => [
                'data' => $history->map(fn (userDeposit $item) => $this->transformDeposit($item))->values()->all(),
                'sum' => (float) $history->sum('amount'),
            ],
        ]);
    }

    public function confirm(userDeposit $deposit): RedirectResponse
    {
        $deposit->user?->increment('coin', $deposit->amount);
        $deposit->confirmed = true;
        $deposit->save();

        return redirect()->back()->with('success', 'User recharged successfully!');
    }

    public function destroy(userDeposit $deposit): RedirectResponse
    {
        $deposit->delete();

        return redirect()->back()->with('success', 'Deleted !');
    }

    private function queryDeposits(string $status, string $find, ?string $sdate, ?string $edate)
    {
        $query = userDeposit::query()->with('user');

        if ($status !== '*') {
            $query->where(['confirmed' => $status]);
        }

        if ($find !== '') {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('amount', 'like', '%' . $find . '%')
                    ->orWhere('senderAccountNumber', 'like', '%' . $find . '%')
                    ->orWhere('paymentMethod', 'like', '%' . $find . '%')
                    ->orWhere('receiverAccountNumber', 'like', '%' . $find . '%')
                    ->orWhere('transactionId', 'like', '%' . $find . '%')
                    ->orWhereHas('user', function ($userQuery) use ($find) {
                        $userQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%')
                            ->orWhere('phone', 'like', '%' . $find . '%');
                    });
            });
        }

        $this->applyDateFilter($query, $sdate, $edate);

        return $query;
    }

    private function transformDeposit(userDeposit $item): array
    {
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
    }

    private function applyDateFilter($query, ?string $sdate, ?string $edate): void
    {
        if (!empty($sdate) && !empty($edate)) {
            $start = Carbon::parse($sdate)->startOfDay();
            $end = Carbon::parse($edate)->endOfDay();

            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $query->whereBetween('created_at', [$start, $end]);

            return;
        }

        if (!empty($sdate)) {
            $query->whereBetween('created_at', [
                Carbon::parse($sdate)->startOfDay(),
                Carbon::parse($sdate)->endOfDay(),
            ]);

            return;
        }

        if (!empty($edate)) {
            $query->whereBetween('created_at', [
                Carbon::parse($edate)->startOfDay(),
                Carbon::parse($edate)->endOfDay(),
            ]);
        }
    }
}
