<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DeveloperAccess;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeveloperController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $developerRequest = DeveloperAccess::where('applied_id', $user->id)->first();

        return Inertia::render('User/Partnership/Developer', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'hasApplied' => (bool) $developerRequest,
            'developerRequest' => $developerRequest ? [
                'id' => $developerRequest->id,
                'status' => $developerRequest->status,
            ] : null,
        ]);
    }

    public function apply(Request $request)
    {
        $exists = DeveloperAccess::where('applied_id', $request->user()->id)->exists();

        if ($exists) {
            return redirect()->back()->with('warning', 'You already applied!');
        }

        $validated = $request->validate([
            'message' => 'nullable|max:500',
        ]);

        DeveloperAccess::create([
            'applied_id' => $request->user()->id,
            'message' => $validated['message'] ?? null,
            'status' => null,
        ]);

        return redirect()->route('user.developer')->with('success', 'Successfully applied!!!');
    }
}

