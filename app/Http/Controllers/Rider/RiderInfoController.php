<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RiderInfoController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $rider = $user?->isRider();

        return Inertia::render('Rider/Info', [
            'rider' => [
                'name' => $user?->name,
                'email' => $user?->email,
                'phone' => $user?->phone,
                'joined' => $rider?->created_at?->toFormattedDateString(),
                'status' => $rider?->status,
                'is_reject' => (bool) ($rider?->is_reject ?? false),
                'reject_fo' => $rider?->reject_fo,
                'targeted_area' => $rider?->targeted_area,
                'fixed_address' => $rider?->fixed_address,
                'current_address' => $rider?->current_address,
                'nid' => $rider?->nid,
                'nid_photo_front' => $rider?->nid_photo_front,
                'nid_photo_back' => $rider?->nid_photo_back,
                'nid_photo_front_url' => $rider?->nid_photo_front ? asset('storage/' . $rider->nid_photo_front) : null,
                'nid_photo_back_url' => $rider?->nid_photo_back ? asset('storage/' . $rider->nid_photo_back) : null,
            ],
        ]);
    }
}
