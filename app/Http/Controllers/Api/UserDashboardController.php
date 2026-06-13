<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use App\Models\UserHasRefs;
use App\Support\ReferralChangePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $currentLevel = $user->currentLevel;
        $myRef = $user->myRef;

        $current = [
            'name' => $currentLevel?->name ?? 'Level 0',
            'req_users' => !empty($myRef?->ref) ? User::where('reference', $myRef->ref)->count() : 0,
            'vip_users' => $user->getMyvipRef()?->count() ?? 0,
            'rewards' => null,
        ];

        $level = Level::where('id', ($user->current_level_id + 1))->first();
        $upcoming = $level ? [
            'name' => $level->name,
            'req_users' => $level->req_users,
            'vip_users' => $level->vip_users,
            'rewards' => $level->rewards,
        ] : [
            'name' => 'Max',
            'req_users' => null,
            'vip_users' => null,
            'rewards' => null,
        ];

        return ApiResponse::success([
            'user_my_ref' => $myRef?->ref,
            'applied_ref' => $user->reference && $user->reference !== config('app.ref') ? $user->reference : '',
            'hide_claim' => false,
            'ref_claim' => ReferralChangePolicy::status($user),
            'joined' => $user->created_at->diffForHumans(),
            'roles' => $user->roles->pluck('name')->values(),
            'active_nav' => $user->active_nav,
            'widgets' => [
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
            ],
            'vendor_active' => $user->requestsToBeVendor()->where('status', 'Active')->first(),
            'reseller_active' => $user->requestsToBeReseller()->where('status', 'Active')->first(),
        ], 'Dashboard fetched');
    }

    public function checkRef(Request $request)
    {
        $validated = $request->validate([
            'newRef' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (!config('app.comission')) {
            return ApiResponse::error('Commission disabled', null, 422);
        }

        $ref = UserHasRefs::where('ref', $validated['newRef'])->first();
        $refClaim = ReferralChangePolicy::status($user);

        if (!$refClaim['can_apply']) {
            return ApiResponse::error($refClaim['message'] ?? 'You can not update your ref', null, 422);
        }

        if ($ref && (int) $ref->owner->id !== (int) $user->id) {
            $user->reference = $validated['newRef'];
            ReferralChangePolicy::markApplied($user);
            $user->save();

            return ApiResponse::success($user->fresh(), 'Ref Accepted');
        }

        return ApiResponse::error('Try Again', null, 422);
    }

    public function ref(Request $request)
    {
        $user = $request->user();
        $refCode = $user->myRef?->ref;
        $refUsers = collect();

        if ($refCode) {
            $refUsers = User::where('reference', $refCode)
                ->latest('id')
                ->get()
                ->map(fn (User $refUser) => [
                    'id' => $refUser->id,
                    'name' => $refUser->name,
                    'comission' => 0,
                    'join' => Carbon::parse($refUser->updated_at)->toFormattedDateString(),
                ])
                ->values();
        }

        return ApiResponse::success([
            'refUsers' => $refUsers,
            'refOwnerName' => $user->getReffOwner?->owner?->name ?? 'User Not Found',
            'totalRefUsers' => $refUsers->count(),
        ], 'Ref fetched');
    }
}
