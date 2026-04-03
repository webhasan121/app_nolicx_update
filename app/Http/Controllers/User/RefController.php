<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RefController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $refUsers = User::where(['reference' => $user->myRef->ref])
            ->latest('id')
            ->get()
            ->map(function ($refUser) {
                return [
                    'id' => $refUser->id,
                    'name' => $refUser->name,
                    'comission' => 0,
                    'join' => Carbon::parse($refUser->updated_at)->toFormattedDateString(),
                ];
            });

        return Inertia::render('User/Refs', [
            'refUsers' => $refUsers,
            'refOwnerName' => $user->getReffOwner?->owner?->name ?? 'User Not Found',
            'totalRefUsers' => $refUsers->count(),
        ]);
    }
}

