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
        ['account' => $account, 'shop' => $shop] = $this->resolveShopContext();

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
        ['shop' => $shop] = $this->resolveShopContext();
        if (! $shop) {
            return redirect()->back()->with('error', 'Active shop not found');
        }

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
        $shop->update($shopArray);
        return redirect()->back()->with('success', 'Shop updated successfully');
    }

    private function resolveShopContext(): array
    {
        $user = auth()->user();
        $account = $user?->active_nav;
        $resellerShop = $user?->resellerShop();
        $vendorShop = $user?->vendorShop();

        if ($account === 'reseller' && $resellerShop) {
            $this->syncDashboardIdentity('reseller');
            return ['account' => 'reseller', 'shop' => $resellerShop];
        }

        if ($account === 'vendor' && $vendorShop) {
            $this->syncDashboardIdentity('vendor');
            return ['account' => 'vendor', 'shop' => $vendorShop];
        }

        if ($resellerShop) {
            $this->syncDashboardIdentity('reseller');
            return ['account' => 'reseller', 'shop' => $resellerShop];
        }

        if ($vendorShop) {
            $this->syncDashboardIdentity('vendor');
            return ['account' => 'vendor', 'shop' => $vendorShop];
        }

        return ['account' => $account, 'shop' => null];
    }

    private function syncDashboardIdentity(string $role): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        if ($user->active_nav !== $role) {
            $user->active_nav = $role;
            $user->save();
        }
    }
}
