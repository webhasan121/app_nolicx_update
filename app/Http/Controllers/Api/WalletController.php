<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DistributeComissions;
use App\Models\TakeComissions;
use App\Models\UserTask;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $find = trim((string) $request->query('find', ''));

        $pendingWithdraws = Withdraw::query()
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->whereNull('is_rejected')
            ->when($find !== '', fn ($query) => $this->applyWithdrawSearch($query, $find))
            ->latest('id')
            ->limit((int) $request->input('limit', 20))
            ->get()
            ->map(fn (Withdraw $withdraw) => $this->withdrawPayload($withdraw))
            ->values();

        $commission = DistributeComissions::where([
            'user_id' => $user->id,
            'confirmed' => true,
        ])->whereDate('updated_at', today())->sum('amount');

        $cut = TakeComissions::where([
            'user_id' => $user->id,
            'confirmed' => true,
        ])->whereDate('updated_at', today())->sum('take_comission');

        $referrer = $user->getMyvipRef()->whereDate('updated_at', today())->sum('comission');
        $task = UserTask::where('user_id', $user->id)->whereDate('updated_at', today())->first();

        return response()->json([
            'success' => true,
            'message' => 'Wallet fetched',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'coin' => (float) $user->coin,
                'available_balance' => (float) $user->abailCoin(),
                'today' => [
                    'task' => $task ? [
                        'id' => $task->id,
                        'earning' => (float) ($task->coin ?? 0),
                        'time' => (int) ($task->time ?? 0),
                    ] : null,
                    'commission' => (float) $commission,
                    'cut' => (float) $cut,
                    'referrer' => (float) $referrer,
                ],
                'pending_withdraws' => $pendingWithdraws,
            ],
        ]);
    }

    public function withdrawHistory(Request $request)
    {
        $find = trim((string) $request->query('find', ''));
        $limit = max(1, min((int) $request->query('limit', 20), 100));

        $withdraws = Withdraw::query()
            ->where('user_id', $request->user()->id)
            ->when($find !== '', fn ($query) => $this->applyWithdrawSearch($query, $find))
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(fn (Withdraw $withdraw) => $this->withdrawPayload($withdraw))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Withdraw history fetched',
            'data' => $withdraws,
        ]);
    }

    public function withdrawIndex(Request $request)
    {
        $withdraws = $request->user()
            ->myWithdraw()
            ->latest('id')
            ->get()
            ->map(fn (Withdraw $withdraw) => $this->withdrawPayload($withdraw))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Withdraws fetched',
            'data' => [
                'available_balance' => (float) $request->user()->abailCoin(),
                'withdraw' => $withdraws,
            ],
        ]);
    }

    public function withdrawCreate(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Withdraw create data fetched',
            'data' => [
                'available_balance' => (float) $request->user()->abailCoin(),
                'phone' => $request->user()->phone,
            ],
        ]);
    }

    public function withdrawRequest(Request $request)
    {
        $user = $request->user();

        if ($user->myWithdraw()->where('status', 0)->whereNull('is_rejected')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'A request is already pending. You are unable to request again!',
                'errors' => null,
            ], 409);
        }

        $validated = $request->validate([
            'pay_to' => ['required', 'string', 'max:255'],
            'pay_by' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'phone' => ['required', 'string', 'max:25'],
        ]);

        if (!$user->haveEnoughBalance($validated['amount'])['status']) {
            return response()->json([
                'success' => false,
                'message' => 'You Have Too Less Balance',
                'errors' => [
                    'amount' => ['You Have Too Less Balance'],
                ],
            ], 422);
        }

        $totalFee = $validated['amount'] * .05;
        $withdraw = DB::transaction(function () use ($validated, $user, $totalFee) {
            return Withdraw::create([
                ...$validated,
                'user_id' => $user->id,
                'status' => 0,
                'fee_range' => '5',
                'total_fee' => $totalFee,
                'maintenance_fee' => $validated['amount'] * .03,
                'server_fee' => $validated['amount'] * .02,
                'payable_amount' => $validated['amount'] - $totalFee,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Withdraw request submitted',
            'data' => $this->withdrawPayload($withdraw),
        ], 201);
    }

    public function withdrawCancel(Request $request)
    {
        $validated = $request->validate([
            'wid' => ['required', 'integer'],
        ]);

        $withdraw = Withdraw::query()
            ->where('id', $validated['wid'])
            ->where('user_id', $request->user()->id)
            ->where('status', 0)
            ->whereNull('is_rejected')
            ->first();

        if (!$withdraw) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to cancel this request.',
                'errors' => null,
            ], 422);
        }

        $withdraw->is_rejected = true;
        $withdraw->reject_for = 'Canceled by user';
        $withdraw->save();

        return response()->json([
            'success' => true,
            'message' => 'Withdraw request canceled.',
            'data' => $this->withdrawPayload($withdraw),
        ]);
    }

    private function applyWithdrawSearch($query, string $find): void
    {
        $query->where(function ($subQuery) use ($find) {
            $subQuery
                ->where('id', 'like', '%' . $find . '%')
                ->orWhere('amount', 'like', '%' . $find . '%')
                ->orWhere('pay_by', 'like', '%' . $find . '%')
                ->orWhere('pay_to', 'like', '%' . $find . '%');
        });
    }

    private function withdrawPayload(Withdraw $withdraw): array
    {
        return [
            'id' => $withdraw->id,
            'amount' => (float) $withdraw->amount,
            'payable_amount' => (float) ($withdraw->payable_amount ?? 0),
            'total_fee' => (float) ($withdraw->total_fee ?? 0),
            'fee_range' => $withdraw->fee_range,
            'maintenance_fee' => (float) ($withdraw->maintenance_fee ?? 0),
            'server_fee' => (float) ($withdraw->server_fee ?? 0),
            'pay_by' => $withdraw->pay_by,
            'pay_to' => $withdraw->pay_to,
            'phone' => $withdraw->phone,
            'status' => (bool) $withdraw->status,
            'status_text' => $withdraw->status ? 'Confirm' : 'Pending',
            'is_rejected' => (bool) $withdraw->is_rejected,
            'reject_for' => $withdraw->reject_for,
            'created_at' => $withdraw->created_at?->toDateTimeString(),
            'created_at_human' => $withdraw->created_at?->diffForHumans(),
        ];
    }
}
