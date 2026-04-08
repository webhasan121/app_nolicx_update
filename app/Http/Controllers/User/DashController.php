<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use App\Models\user_has_refs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $currentLevel = $user->currentLevel;
        $myRef = $user->myRef;

        $current = [
            'name' => $currentLevel?->name ?? 'Level 0',
            'req_users' => !empty($myRef?->ref) ? User::where('reference', $myRef->ref)->count() : 0,
            'vip_users' => $user->getMyvipRef()?->count() ?? 0,
            'rewards' => null,
        ];

        $level = Level::where('id', ($user->current_level_id + 1))->first();

        if ($level) {
            $upcoming = [
                'name' => $level->name,
                'req_users' => $level->req_users,
                'vip_users' => $level->vip_users,
                'rewards' => $level->rewards,
            ];
        } else {
            $upcoming = [
                'name' => 'Max',
                'req_users' => null,
                'vip_users' => null,
                'rewards' => null,
            ];
        }

        $widgets = [
            [
                'name' => $current['name'],
                'data' => [
                    'req_users' => $current['req_users'],
                    'vip_users' => $current['vip_users'],
                ],
                'rewards' => $current['rewards'],
            ],
            [
                'name' => $upcoming['name'],
                'data' => [
                    'req_users' => $upcoming['req_users'],
                    'vip_users' => $upcoming['vip_users'],
                ],
                'rewards' => $upcoming['rewards'],
            ],
        ];

        return Inertia::render('User/Dash', [
            'user_my_ref' => $myRef?->ref ?? null,
            'hide_claim' => $user->created_at->diffInHours(
                Carbon::now()
            ) > 72,
            'joined' => $user->created_at->diffForHumans(),
            'roles' => $user->roles->pluck('name') ?? [],
            'active_nav' => $user->active_nav,
            'widgets' => $widgets,
            'vendorActive' => $user
                ->requestsToBeVendor()
                ->where('status', 'Active')
                ->first(),
            'resellerActive' => $user
                ->requestsToBeReseller()
                ->where('status', 'Active')
                ->first(),
        ]);
    }

    public function checkRef(Request $request)
    {
        $request->validate([
            'newRef' => 'required'
        ]);

        $user = auth()->user();

        if (!config('app.comission')) {
            return back()->with('warning', 'Commission disabled');
        }

        $reference = $request->newRef;

        $reff = user_has_refs::where('ref', $reference)->first();

        if ($user->created_at->diffInHours(Carbon::now()) > 72 || $user->reference_accepted_at) {
            return back()->with('info', 'Time Up. You can not update your ref');
        }

        if ($reff && $reff->owner->id != $user->id) {
            $user->reference = $reference;
            $user->reference_accepted_at = today();
            $user->save();

            return back()->with('success', 'Ref Accepted');
        }

        return back()->with('warning', 'Try Again');
    }
}
