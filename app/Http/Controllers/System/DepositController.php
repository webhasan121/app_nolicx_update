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
        $status = $request->query('status', '0');
        $sdate = $request->query('sdate', now()->subDay(30)->format('Y-m-d'));
        $edate = $request->query('edate', today()->format('Y-m-d'));

        $history = $this->queryDeposits($status, $sdate, $edate)
            ->orderBy('id', 'desc')
            ->paginate((int) config('app.config'))
            ->withQueryString();

        return Inertia::render('Auth/system/deposit/index', [
            'status' => (string) $status,
            'sdate' => $sdate,
            'edate' => $edate,
            'history' => [
                'data' => $history->getCollection()->map(fn (userDeposit $item) => $this->transformDeposit($item))->values()->all(),
                'links' => $history->linkCollection()->toArray(),
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
        $status = $request->query('status', '0');
        $sdate = $request->query('sdate', now()->subDay(30)->format('Y-m-d'));
        $edate = $request->query('edate', today()->format('Y-m-d'));

        $history = $this->queryDeposits($status, $sdate, $edate)
            ->orderBy('id', 'desc')
            ->paginate((int) config('app.config'))
            ->withQueryString();

        return Inertia::render('Auth/system/deposit/PrintSummery', [
            'status' => (string) $status,
            'sdate' => $sdate,
            'edate' => $edate,
            'history' => [
                'data' => $history->getCollection()->map(fn (userDeposit $item) => $this->transformDeposit($item))->values()->all(),
                'sum' => (float) $history->getCollection()->sum('amount'),
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

    private function queryDeposits(string $status, string $sdate, string $edate)
    {
        return userDeposit::query()
            ->with('user')
            ->where(['confirmed' => $status])
            ->whereBetween('created_at', [$sdate, Carbon::parse($edate)->endOfDay()]);
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
}
