<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LevelHistory;
use App\Models\User;
use App\Models\UserHasRefs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $myRef = $user->myRef;
        $reqUsers = !empty($myRef?->ref) ? User::where('reference', $myRef->ref)->count() : 0;
        $vipUsers = $user->getMyvipRef()?->count() ?? 0;
        $levels = Level::query()
            ->where('status', true)
            ->orderBy('req_users')
            ->orderBy('vip_users')
            ->orderBy('id')
            ->get();
        $matchedLevel = $levels
            ->filter(fn(Level $level) => $reqUsers >= (int) $level->req_users && $vipUsers >= (int) $level->vip_users)
            ->sortByDesc('id')
            ->first();

        if (!$matchedLevel) {
            $matchedLevel = $levels->first();
        }

        if ($matchedLevel && (int) $user->current_level_id !== (int) $matchedLevel->id) {
            LevelHistory::create([
                'user_id' => $user->id,
                'from_level_id' => $user->current_level_id,
                'to_level_id' => $matchedLevel->id,
            ]);

            $user->forceFill(['current_level_id' => $matchedLevel->id])->save();
            $user->setRelation('currentLevel', $matchedLevel);
        }

        $upcomingLevel = $matchedLevel
            ? $levels->first(fn(Level $level) => (int) $level->id > (int) $matchedLevel->id)
            : null;

        $current = [
            'name' => $matchedLevel?->name ?? 'Star-0',
            'req_users' => $reqUsers,
            'vip_users' => $vipUsers,
            'rewards' => null,
        ];

        if ($upcomingLevel) {
            $upcoming = [
                'name' => $upcomingLevel->name,
                'req_users' => $upcomingLevel->req_users,
                'vip_users' => $upcomingLevel->vip_users,
                'rewards' => $upcomingLevel->rewards,
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

        $reff = UserHasRefs::where('ref', $reference)->first();

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
