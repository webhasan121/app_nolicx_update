<?php

namespace App\Http\Controllers;

use App\HandleImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResellerShopController extends Controller
{
    use HandleImageUpload;

    public function show(Request $request, string $user): Response
    {
        $account = auth()->user()->active_nav;
        $shop = null;

        if ($account === 'reseller') {
            $shop = auth()->user()->resellerShop();
        }

        if ($account === 'vendor') {
            $shop = auth()->user()->vendorShop();
        }

        $shopArray = $shop ? $shop->toArray() : [];

        return Inertia::render('Reseller/Shop', [
            'account' => $account,
            'shop' => [
                ...$shopArray,
                'logo_url' => !empty($shopArray['logo']) ? asset('storage/' . $shopArray['logo']) : null,
                'banner_url' => !empty($shopArray['banner']) ? asset('storage/' . $shopArray['banner']) : null,
            ],
        ]);
    }

    public function update(Request $request, string $user): RedirectResponse
    {

        $shopArray = $request->only([
            'id',
            'shop_name_en',
            'email',
            'phone',
            'address',
            'district',
            'upozila',
            'village',
            'zip',
            'road_no',
            'system_get_comission',
            'max_product_upload',
            'max_resell_product',
            'logo',
            'banner',
            'description',
        ]);

        if ($request->hasFile('newLogo')) {
            $shopArray['logo'] = $this->handleImageUpload(
                $request->file('newLogo'),
                'shop-logo',
                $shopArray['logo'] ?? null
            );
        }

        if ($request->hasFile('newBanner')) {
            $shopArray['banner'] = $this->handleImageUpload(
                $request->file('newBanner'),
                'shop-banner',
                $shopArray['banner'] ?? null
            );
        }

        // echo '<pre>';
        // print_r($shopArray);
        // echo '</pre>';
        // exit();
        // address
        auth()->user()->resellerShop()->update($shopArray);

        return redirect()->back()->with('success', 'updated');
    }
}
