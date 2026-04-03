<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ManagementAccess;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $managementRequest = ManagementAccess::where('applied_id', $user->id)->first();

        return Inertia::render('User/Partnership/Management', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'hasApplied' => (bool) $managementRequest,
            'managementRequest' => $managementRequest ? [
                'id' => $managementRequest->id,
                'status' => $managementRequest->status,
            ] : null,
        ]);
    }

    public function apply(Request $request)
    {
        $exists = ManagementAccess::where('applied_id', $request->user()->id)->exists();

        if ($exists) {
            return redirect()->back()->with('warning', 'You already applied!');
        }

        $validated = $request->validate([
            'message' => 'nullable|max:500',
        ]);

        ManagementAccess::create([
            'applied_id' => $request->user()->id,
            'message' => $validated['message'] ?? null,
            'status' => null,
        ]);

        return redirect()->route('user.management')->with('success', 'Successfully applied!!!');
    }
}

