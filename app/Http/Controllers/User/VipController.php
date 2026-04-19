<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Packages;
use App\Models\vip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class VipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $packages = Packages::all();

        $vip = vip::with('package')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) use ($user) {

                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'task_type' => $item->task_type,
                    'package_id' => $item->package_id,

                    'package' => [
                        'id' => $item->package?->id,
                        'name' => $item->package?->name,
                        'coin' => $item->package?->coin,
                        'countdown' => $item->package?->countdown,
                    ],

                    'created_at_human' => $item->created_at
                        ? $item->created_at->diffForHumans()
                        : null,

                    'valid_till_human' => $item->valid_till
                        ? Carbon::parse($item->valid_till)->diffForHumans()
                        : 'Unlimited',

                    'completed_tasks' => DB::table('user_tasks')
                        ->where([
                            'user_id' => $user->id,
                            'package_id' => $item->package_id,
                        ])->count(),
                ];
            });

        return Inertia::render('User/Vip/Index', [
            'vip' => $vip,
            'packages' => $packages,
        ]);
    }
}
