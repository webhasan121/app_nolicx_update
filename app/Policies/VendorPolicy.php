<?php

namespace App\Policies;

use App\Models\User;
use App\Models\vendor;
use Illuminate\Support\Facades\Auth;

class VendorPolicy
{

    /**
     * determined if the given user can perform the given action on the vendor.
     * if the user is the vendor, they can perform the action.
     * if the vendor membership is on pending, only the user can't perform the action.
     */
    public function update(vendor $vendor)
    {
        // if the vendor membership in on pending, user can update the request
        if (auth()->user()->can('manage_vendor') || (Auth::id() == $vendor->user_id && $vendor->status == 'Pending')) {
            return true;
        }
    }
}
