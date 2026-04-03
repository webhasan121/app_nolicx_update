<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Withdraw;
use App\Rules\HaveEnoughBalance;

class WithdrawController extends Controller
{

    // return create form
    public function create(Request $request)
    {
        // 
    }


    // request form user
    public function storeFromUser(Request $request)
    {
        if (auth()->user()->myWithdraw()->where(['status' => 0])->exists()) {
            return redirect()->back()->withInput()->with('warning', "A Request already in Pending. You are unable to request again!");
        }

        $validated = $request->validate(
            [
                'pay_to' => 'required',
                'pay_by' => 'required',
                'amount' => ['required', new HaveEnoughBalance()],
                'phone' => 'required',
            ],
            [
                'pay_to.required' => 'Give A Number to receive payment.',
                'pay_by.required' => 'Select A payment Method.',
                'amount.required' => 'Give Amount .'
            ]
        );
        // related data
        $tp = $request->amount * .05;
        $payable = $request->amount - $tp;

        $data = array(
            'user_id' => auth()->user()->id,
            'status' => 0,
            'fee_range' => '5',
            'total_fee' => $tp,
            'maintenance_fee' => $request->amount * .03,
            'server_fee' => $request->amount * .02,
            'payable_amount' => $payable,
        );


        try {
            //code...
            DB::transaction(function () use ($validated, $data) {
                Withdraw::create(
                    array_merge($validated, $data)
                );
            });
            return redirect()->route('user.wallet.withdraw');
        } catch (\Throwable $th) {
            return redirect()->back()->with('warning', 'Unable to process your request !');
            // throw $th->getMessage();
        }
    }

    public function destroyFromUser(Request $request)
    {
        $request->validate([
            'wid' => 'required|integer',
        ]);

        $withdraw = Withdraw::query()
            ->where('id', $request->wid)
            ->where('user_id', auth()->id())
            ->where('status', 0)
            ->whereNull('is_rejected')
            ->first();

        if (!$withdraw) {
            return redirect()->back()->with('warning', 'Unable to cancel this request.');
        }

        $withdraw->is_rejected = true;
        $withdraw->reject_for = 'Canceled by user';
        $withdraw->save();

        return redirect()->back()->with('success', 'Withdraw request canceled.');
    }


    private function store(Request $reqeust)
    {
        // handle store, and return a successk
    }
}
