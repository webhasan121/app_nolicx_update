<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserWalletController;
use App\Models\Withdraw;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class WithdrawController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $where = $request->query('where');
        $q = $request->query('q');
        $fst = $request->query('fst', 'Pending');
        $sdate = $request->query('sdate', now()->subDay(30)->format('Y-m-d'));
        $edate = $request->query('edate', now()->format('Y-m-d'));

        $statsQuery = Withdraw::query();

        $qry = Withdraw::query()->with('user');

        if ($fst === 'Reject') {
            $qry->rejected();
        }

        if ($fst !== 'Reject') {
            $sts = $fst === 'Accept' ? 1 : 0;
            $qry->where(['status' => $sts]);
        }

        if ($where === 'query') {
            $qry->whereHas('user', function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%');
            });
        } elseif ($where === 'find') {
            $qry->where('id', $q);
        }

        $withdraw = $qry
            ->whereBetween('created_at', [$sdate, \Illuminate\Support\Carbon::parse($edate)->endOfDay()])
            ->orderBy('id', 'desc')
            ->paginate(config('app.pagination'))
            ->withQueryString();

        return Inertia::render('Auth/system/withdraw/index', [
            'filters' => compact('where', 'q', 'fst', 'sdate', 'edate'),
            'stats' => [
                'total' => $statsQuery->count(),
                'pending' => Withdraw::pending()->count(),
                'paid' => Withdraw::accepted()->count(),
                'reject' => Withdraw::rejected()->count(),
                'amount' => $withdraw->sum('amount'),
                'payable' => $withdraw->sum('payable_amount'),
                'server_fee' => $withdraw->sum('server_fee'),
                'maintenance_fee' => $withdraw->sum('maintenance_fee'),
            ],
            'withdraw' => [
                'data' => $withdraw->getCollection()->map(fn (Withdraw $item) => [
                    'id' => $item->id,
                    'seen_by_admin' => (bool) $item->seen_by_admin,
                    'amount' => $item->amount ?? 0,
                    'status' => (bool) $item->status,
                    'is_rejected' => $item->is_rejected,
                    'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                    'user' => [
                        'name' => $item->user?->name,
                        'email' => $item->user?->email,
                        'subscription' => (bool) $item->user?->subscription,
                    ],
                ])->values()->all(),
                'links' => $withdraw->linkCollection()->toArray(),
                'sum_amount' => $withdraw->sum('amount'),
            ],
        ]);
    }

    public function viewReact(int $id): Response
    {
        $withdraw = Withdraw::with('user')->findOrFail($id);

        if (!$withdraw->seen_by_admin) {
            $withdraw->seen_by_admin = now();
            $withdraw->save();
        }

        return Inertia::render('Auth/system/withdraw/View', [
            'withdraw' => [
                'id' => $withdraw->id,
                'amount' => $withdraw->amount,
                'payable_amount' => $withdraw->payable_amount ?? 0,
                'fee_range' => $withdraw->fee_range ?? 0,
                'total_fee' => $withdraw->total_fee ?? 0,
                'server_fee' => $withdraw->server_fee ?? 0,
                'maintenance_fee' => $withdraw->maintenance_fee ?? 0,
                'status' => (bool) $withdraw->status,
                'is_rejected' => (bool) $withdraw->is_rejected,
                'reject_for' => $withdraw->reject_for,
                'pay_by' => $withdraw->pay_by,
                'pay_to' => $withdraw->pay_to,
                'bank_account' => $withdraw->bank_account,
                'account_holder_name' => $withdraw->account_holder_name,
                'account_humber' => $withdraw->account_humber,
                'paid_from' => $withdraw->paid_from,
                'transaction_id' => $withdraw->transaction_id,
                'confirm_by' => $withdraw->confirm_by,
                'updated_at_formatted' => $withdraw->updated_at ? Carbon::parse($withdraw->updated_at)->format('m:s') : null,
                'user' => [
                    'currency_sing' => $withdraw->user?->currency_sing,
                    'abail_coin' => $withdraw->user?->abailCoin(),
                ],
            ],
        ]);
    }

    public function printReact(Request $request): Response
    {
        $sdate = $request->query('sdate', now()->subDay(30)->format('Y-m-d'));
        $edate = $request->query('edate', now()->format('Y-m-d'));
        $fst = $request->query('fst', 'Accept');
        $q = $request->query('q');
        $where = $request->query('where');

        $qry = Withdraw::query()->with('user');

        if ($fst === 'Reject') {
            $qry->rejected();
        }

        if ($fst !== 'Reject') {
            $sts = $fst === 'Accept' ? 1 : 0;
            $qry->where(['status' => $sts]);
        }

        if ($where === 'query') {
            $qry->whereHas('user', function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%');
            });
        } elseif ($where === 'find') {
            $qry->where('id', $q);
        }

        $withdraws = $qry
            ->whereBetween('created_at', [$sdate, Carbon::parse($edate)->endOfDay()])
            ->orderBy('id', 'desc')
            ->paginate(config('app.pagination'));

        return Inertia::render('Auth/system/withdraw/Print', [
            'filters' => [
                'sdate_formatted' => Carbon::parse($sdate)->format('d/m/Y'),
                'edate_formatted' => Carbon::parse($edate)->format('d/m/Y'),
            ],
            'withdraws' => $withdraws->getCollection()->map(fn (Withdraw $item) => [
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
            ])->values()->all(),
            'summary' => [
                'sum_amount' => $withdraws->sum('amount'),
                'sum_total_fee' => $withdraws->sum('total_fee'),
                'sum_payable_amount' => $withdraws->sum('payable_amount'),
            ],
        ]);
    }

    public function confirmPayment(Request $request, int $id): RedirectResponse
    {
        $withdraw = Withdraw::with('user')->findOrFail($id);

        if ($withdraw->user?->abailCoin() > $withdraw->amount) {
            if (!$withdraw->is_rejected) {
                $validated = $request->validate([
                    'paid_from' => 'required',
                    'trx' => 'required',
                ]);

                $withdraw->forceFill([
                    'confirmed_by' => auth()->user()->name . "-" . auth()->user()->email,
                    'status' => true,
                    'paid_from' => $validated['paid_from'],
                    'transaction_id' => $validated['trx'],
                ])->save();

                UserWalletController::remove($withdraw->user_id, $withdraw->amount);

                return redirect()->back()->with('success', 'Withdraw Confirmed !');
            }

            return redirect()->back()->with('error', 'Payment already Rejected !');
        }

        return redirect()->back()->with('warning', 'User too low balance !');
    }

    public function rejectPayment(Request $request, int $id): RedirectResponse
    {
        $withdraw = Withdraw::findOrFail($id);

        if ($withdraw->status == false) {
            $validated = $request->validate([
                'rMessage' => 'required',
            ]);

            $withdraw->status = false;
            $withdraw->is_rejected = true;
            $withdraw->reject_for = $validated['rMessage'];
            $withdraw->save();

            return redirect()->back()->with('success', 'Withdraw Rejected !');
        }

        return redirect()->back()->with('error', 'Withdraw Request Already Accept !');
    }
}
