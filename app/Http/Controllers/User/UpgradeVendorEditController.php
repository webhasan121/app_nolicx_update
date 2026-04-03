<?php

namespace App\Http\Controllers\User;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\reseller;
use App\Models\reseller_has_document;
use App\Models\vendor;
use App\Models\vendor_has_document;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UpgradeVendorEditController extends Controller
{
    use HandleImageUpload;

    public function edit(Request $request, $id)
    {
        $upgrade = $request->query('upgrade', 'vendor');
        $nav = $request->query('nav', 'basic');

        if (!in_array($upgrade, ['vendor', 'reseller'], true)) {
            $upgrade = 'vendor';
        }

        if (!in_array($nav, ['basic', 'document'], true)) {
            $nav = 'basic';
        }

        $data = $upgrade === 'reseller'
            ? $request->user()->requestsToBeReseller()->find($id)
            : $request->user()->requestsToBeVendor()->find($id);

        if (!$data) {
            return redirect()
                ->route('upgrade.vendor.index', ['upgrade' => $upgrade])
                ->with('error', 'Unable to edit or update');
        }

        $document = $data->documents;

        return Inertia::render('User/Upgrade/Vendor/Edit', [
            'id' => (int) $id,
            'upgrade' => $upgrade,
            'nav' => $nav,
            'vendor' => [
                'shop_name_en' => $data->shop_name_en,
                'logo' => $data->logo,
                'logo_url' => $data->logo ? asset('storage/' . $data->logo) : null,
                'banner' => $data->banner,
                'banner_url' => $data->banner ? asset('storage/' . $data->banner) : null,
                'phone' => $data->phone,
                'email' => $data->email,
                'country' => $data->country,
                'district' => $data->district,
                'upozila' => $data->upozila,
                'village' => $data->village,
                'zip' => $data->zip,
                'road_no' => $data->road_no,
                'house_no' => $data->house_no,
                'status' => $data->status,
                'system_get_comission' => $data->system_get_comission,
                'rejected_for' => $data->rejected_for,
            ],
            'vendorDocument' => [
                'id' => $document?->id,
                'nid' => $document?->nid,
                'nid_front' => $document?->nid_front,
                'nid_back' => $document?->nid_back,
                'shop_tin' => $document?->shop_tin,
                'shop_tin_image' => $document?->shop_tin_image,
                'shop_trade' => $document?->shop_trade,
                'shop_trade_image' => $document?->shop_trade_image,
                'deatline' => $document?->deatline,
                'nid_front_url' => $document?->nid_front ? asset('storage/' . $document->nid_front) : null,
                'nid_back_url' => $document?->nid_back ? asset('storage/' . $document->nid_back) : null,
                'shop_tin_image_url' => $document?->shop_tin_image ? asset('storage/' . $document->shop_tin_image) : null,
                'shop_trade_image_url' => $document?->shop_trade_image ? asset('storage/' . $document->shop_trade_image) : null,
            ],
            'authRequest' => [
                'status' => $data->status,
                'system_get_comission' => $data->system_get_comission,
                'rejected_for' => $data->rejected_for,
                'documents' => [
                    'deatline' => $document?->deatline,
                ],
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $upgrade = $request->input('upgrade', $request->query('upgrade', 'vendor'));
        if (!in_array($upgrade, ['vendor', 'reseller'], true)) {
            $upgrade = 'vendor';
        }

        $data = $upgrade === 'reseller'
            ? $request->user()->requestsToBeReseller()->find($id)
            : $request->user()->requestsToBeVendor()->find($id);

        if (!$data) {
            return redirect()->back()->with('warning', 'Unable to Edit or Update');
        }

        $validated = $request->validate([
            'shop_name_en' => ['required', 'string', 'max:100', 'min:5'],
            'phone' => ['required', 'max:11', 'min:10'],
            'email' => ['required', 'email'],
            'country' => ['required', 'string'],
            'district' => ['required', 'string'],
            'upozila' => ['required', 'string'],
            'village' => ['required', 'string'],
            'zip' => ['required'],
            'road_no' => ['required'],
            'house_no' => ['required'],
            'newLogo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'newBanner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);

        $payload = [
            'shop_name_en' => $validated['shop_name_en'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'country' => $validated['country'],
            'district' => $validated['district'],
            'upozila' => $validated['upozila'],
            'village' => $validated['village'],
            'zip' => $validated['zip'],
            'road_no' => $validated['road_no'],
            'house_no' => $validated['house_no'],
            'logo' => $this->handleImageUpload($request->file('newLogo'), 'shop-logo', $data->logo),
            'banner' => $this->handleImageUpload($request->file('newBanner'), 'shop-banner', $data->banner),
        ];

        $data->update($payload);

        return redirect()->back()->with('success', 'Updated');
    }

    public function updateDocument(Request $request, $id)
    {
        $upgrade = $request->input('upgrade', $request->query('upgrade', 'vendor'));
        if (!in_array($upgrade, ['vendor', 'reseller'], true)) {
            $upgrade = 'vendor';
        }

        $data = $upgrade === 'vendor'
            ? vendor_has_document::find($id)
            : reseller_has_document::find($id);

        if (!$data) {
            return redirect()->back()->with('warning', 'Unable to Edit or Update');
        }

        $validated = $request->validate([
            'nid' => ['nullable', 'string'],
            'shop_tin' => ['nullable', 'string'],
            'shop_trade' => ['nullable', 'string'],
            'nid_front' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'nid_back' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'shop_tin_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'shop_trade_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);

        $payload = [
            'nid' => $validated['nid'] ?? $data->nid,
            'shop_tin' => $validated['shop_tin'] ?? $data->shop_tin,
            'shop_trade' => $validated['shop_trade'] ?? $data->shop_trade,
            'nid_front' => $this->handleImageUpload($request->file('nid_front'), 'upgrade-document', $data->nid_front),
            'nid_back' => $this->handleImageUpload($request->file('nid_back'), 'upgrade-document', $data->nid_back),
            'shop_tin_image' => $this->handleImageUpload($request->file('shop_tin_image'), 'upgrade-document', $data->shop_tin_image),
            'shop_trade_image' => $this->handleImageUpload($request->file('shop_trade_image'), 'upgrade-document', $data->shop_trade_image),
        ];

        $data->update($payload);

        return redirect()->back()->with('success', 'Information Updated Successfully');
    }
}

