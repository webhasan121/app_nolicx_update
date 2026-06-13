<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RiderInfoController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $rider = $user?->isRider();
        $frontImageUrl = $rider?->nid_photo_front ? Storage::url($rider->nid_photo_front) : null;
        $backImageUrl = $rider?->nid_photo_back ? Storage::url($rider->nid_photo_back) : null;

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
                'nid_photo_front_url' => $frontImageUrl,
                'nid_photo_back_url' => $backImageUrl,
            ],
        ]);
    }
}
