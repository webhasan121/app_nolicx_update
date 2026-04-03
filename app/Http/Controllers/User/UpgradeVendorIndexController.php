<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UpgradeVendorIndexController extends Controller
{
    public function index(Request $request)
    {
        $upgrade = $request->query('upgrade', 'vendor');
        if (!in_array($upgrade, ['vendor', 'reseller', 'rider'], true)) {
            $upgrade = 'vendor';
        }

        $user = $request->user();

        $vendorRequests = $user->requestsToBeVendor()
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'shop_name_en' => $row->shop_name_en,
                    'status' => $row->status,
                    'created_at' => $row->created_at?->toFormattedDateString(),
                ];
            });

        $resellerRequests = $user->requestsToBeReseller()
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'shop_name_en' => $row->shop_name_en,
                    'status' => $row->status,
                    'created_at' => $row->created_at?->toFormattedDateString(),
                ];
            });

        $vendorActive = $user->requestsToBeVendor()->where('status', 'Active')->first();
        $resellerActive = $user->requestsToBeReseller()->where('status', 'Active')->first();

        return Inertia::render('User/Upgrade/Vendor/Index', [
            'upgrade' => $upgrade,
            'vendor_requests' => $vendorRequests,
            'reseller_requests' => $resellerRequests,
            'vendor_active' => $vendorActive,
            'reseller_active' => $resellerActive,
        ]);
    }
}

