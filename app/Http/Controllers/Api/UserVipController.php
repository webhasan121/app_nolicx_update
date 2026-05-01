<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Packages;
use App\Models\UserTask;
use App\Models\Vip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserVipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return ApiResponse::success([
            'vip' => Vip::with('package')
                ->where('user_id', $user->id)
                ->get()
                ->map(fn (Vip $vip) => $this->vipPayload($vip, $user->id))
                ->values(),
            'packages' => Packages::all()->map(fn (Packages $package) => $this->packagePayload($package))->values(),
        ], 'VIP fetched');
    }

    public function packages()
    {
        return ApiResponse::success(
            Packages::all()->map(fn (Packages $package) => $this->packagePayload($package))->values(),
            'Packages fetched'
        );
    }

    public function packageDetails(int $package)
    {
        $item = Packages::with('payOption')->findOrFail($package);

        return ApiResponse::success([
            'package' => $this->packageDetailsPayload($item),
            'ownerPackage' => 1,
        ], 'Package details fetched');
    }

    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'payment_by' => ['required', 'string', 'max:255'],
            'trx' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:25'],
            'task_type' => ['required', 'string'],
            'nid' => ['required', 'string', 'max:255'],
            'nid_front' => ['required', 'image'],
            'nid_back' => ['required', 'image'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 0;
        $validated['nid_front'] = $request->file('nid_front')->store('vips', 'public');
        $validated['nid_back'] = $request->file('nid_back')->store('vips', 'public');

        $vip = Vip::create($validated);

        return ApiResponse::success($this->vipPayload($vip->load('package'), $request->user()->id), 'Package purchase submitted', 201);
    }

    public function time(Request $request)
    {
        return ApiResponse::success($this->taskData($request), 'Package time fetched');
    }

    public function countTime(Request $request)
    {
        $taskData = $this->taskData($request);

        if (!$taskData['enabled']) {
            return ApiResponse::success($taskData, 'Package time fetched');
        }

        $vip = $request->user()->subscription()->active()->valid()->first();
        $package = $vip?->package;
        $duration = ($package?->countdown ?? 0) * 60;
        $currentTask = UserTask::where([
            'user_id' => $request->user()->id,
            'package_id' => $vip?->package_id,
        ])->whereDate('created_at', today())->first();

        if (($currentTask?->time ?? 0) >= $duration && $taskData['task_not_complete_yet']) {
            $currentTask->coin = $package?->coin;
            $currentTask->save();

            $user = $request->user();
            $user->coin += $package?->coin;
            $user->save();
        } elseif ($taskData['task_not_complete_yet']) {
            if ($currentTask) {
                $currentTask->increment('time');
            } else {
                UserTask::create([
                    'user_id' => $request->user()->id,
                    'package_id' => $package?->id,
                    'vip_id' => $vip?->id,
                    'earn_by' => 'task',
                    'time' => 0,
                ]);
            }
        }

        return ApiResponse::success($this->taskData($request), 'Package time counted');
    }

    private function taskData(Request $request): array
    {
        $vip = $request->user()->subscription()->active()->valid()->first();
        $package = $vip?->package;

        if (!$vip || !$package) {
            return $this->emptyTaskData();
        }

        $currentTask = UserTask::where([
            'user_id' => $request->user()->id,
            'package_id' => $vip->package_id,
        ])->whereDate('created_at', today())->first();

        $lastTask = UserTask::where([
            'user_id' => $request->user()->id,
            'package_id' => $vip->package_id,
        ])->latest()->first();

        $currentTaskTime = $currentTask?->time ?? 0;
        $taskNotCompleteYet = $currentTask?->coin ? false : true;

        if (
            $vip->task_type === 'monthly' &&
            $lastTask?->created_at &&
            Carbon::parse($lastTask->created_at)->shortLocaleMonth === today()->shortLocaleMonth &&
            $lastTask?->coin
        ) {
            $taskNotCompleteYet = false;
        }

        $min = (int) floor($currentTaskTime / 60);
        $sec = $currentTaskTime - ($min * 60);

        return [
            'enabled' => true,
            'vip_id' => $vip->id,
            'package_id' => $package->id,
            'countdown' => (int) ($package->countdown ?? 0),
            'current_time' => $currentTaskTime,
            'duration' => (int) (($package->countdown ?? 0) * 60),
            'task_type' => $vip->task_type,
            'task_not_complete_yet' => $taskNotCompleteYet,
            'min' => $min < 10 ? '0' . $min : (string) $min,
            'sec' => $sec < 10 ? '0' . $sec : (string) $sec,
        ];
    }

    private function emptyTaskData(): array
    {
        return [
            'enabled' => false,
            'countdown' => 0,
            'current_time' => 0,
            'duration' => 0,
            'task_type' => null,
            'task_not_complete_yet' => false,
            'min' => '00',
            'sec' => '00',
        ];
    }

    private function vipPayload(Vip $vip, int $userId): array
    {
        return [
            'id' => $vip->id,
            'status' => $vip->status,
            'task_type' => $vip->task_type,
            'package_id' => $vip->package_id,
            'valid_from' => $vip->valid_from,
            'valid_till' => $vip->valid_till,
            'package' => $vip->package ? $this->packagePayload($vip->package) : null,
            'created_at_human' => $vip->created_at?->diffForHumans(),
            'valid_till_human' => $vip->valid_till ? Carbon::parse($vip->valid_till)->diffForHumans() : 'Unlimited',
            'completed_tasks' => DB::table('user_tasks')
                ->where([
                    'user_id' => $userId,
                    'package_id' => $vip->package_id,
                ])->count(),
        ];
    }

    private function packagePayload(Packages $package): array
    {
        return [
            'id' => $package->id,
            'name' => $package->name,
            'slug' => $package->slug,
            'price' => $package->price,
            'coin' => $package->coin,
            'm_coin' => $package->m_coin,
            'countdown' => $package->countdown,
            'status' => $package->status,
            'description' => $package->description,
        ];
    }

    private function packageDetailsPayload(Packages $package): array
    {
        return [
            ...$this->packagePayload($package),
            'payOption' => $package->payOption->map(fn ($item) => [
                'id' => $item->id,
                'pay_type' => $item->pay_type,
                'pay_to' => $item->pay_to,
            ])->values(),
        ];
    }
}
