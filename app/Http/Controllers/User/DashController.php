<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\user_has_refs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashController extends Controller
{

    public function index()
    {





        return Inertia::render('User/Dash', [
            'user_my_ref' => auth()->user()->myRef->ref ?? null,
            'hide_claim' => auth()->user()->created_at->diffInHours(
                Carbon::now()
            ) > 72,
            'joined' => auth()->user()->created_at->diffForHumans(),
            'roles' => auth()->user()->roles->pluck('name') ?? [],
            'active_nav' => auth()->user()->active_nav,
            // 'roleNames' => auth()->user()->getRoleNames(),
            'vendorActive' => auth()->user()
                ->requestsToBeVendor()
                ->where('status', 'Active')
                ->first(),

            'resellerActive' => auth()->user()
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
