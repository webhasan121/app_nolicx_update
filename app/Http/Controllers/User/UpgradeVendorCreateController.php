<?php

namespace App\Http\Controllers\User;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\country;
use App\Models\reseller;
use App\Models\state;
use App\Models\vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UpgradeVendorCreateController extends Controller
{
    use HandleImageUpload;

    public function create(Request $request)
    {
        $upgrade = $request->query('upgrade', 'vendor');
        if (!in_array($upgrade, ['vendor', 'reseller'], true)) {
            $upgrade = 'vendor';
        }

        $user = $request->user();
        $requestModel = $upgrade === 'vendor'
            ? vendor::where(['user_id' => $user->id])->orderByDesc('id')->first()
            : reseller::where(['user_id' => $user->id])->orderByDesc('id')->first();

        if ($requestModel && $requestModel->status === 'Pending') {
            return redirect()
                ->route('upgrade.vendor.index', ['upgrade' => $upgrade])
                ->with('info', 'Unable to request again, your request is pending');
        }

        if ($requestModel && $requestModel->status === 'Active') {
            return redirect()
                ->route('upgrade.vendor.index', ['upgrade' => $upgrade])
                ->with('info', 'Unable to request again, you have an active Membership');
        }

        $countryId = country::where('name', 'Bangladesh')->value('id');

        return Inertia::render('User/Upgrade/Vendor/Create', [
            'upgrade' => $upgrade,
            'defaults' => [
                'phone' => $user->phone,
                'email' => $user->email,
                'country' => 'Bangladesh',
            ],
            'states' => state::where('country_id', $countryId)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function loadCities($state)
    {
        return response()->json(
            city::where('state_id', $state)
                ->orderBy('name')
                ->get(['id', 'name', 'state_id'])
        );
    }

    public function store(Request $request)
    {
        $upgrade = $request->input('upgrade', 'vendor');
        if (!in_array($upgrade, ['vendor', 'reseller'], true)) {
            $upgrade = 'vendor';
        }

        $user = $request->user();
        $requestModel = $upgrade === 'vendor'
            ? vendor::where(['user_id' => $user->id])->orderByDesc('id')->first()
            : reseller::where(['user_id' => $user->id])->orderByDesc('id')->first();

        if ($requestModel && in_array($requestModel->status, ['Pending', 'Active'], true)) {
            return redirect()
                ->route('upgrade.vendor.index', ['upgrade' => $upgrade])
                ->with('info', 'Unable to request again.');
        }

        $baseRules = [
            'shop_name_en' => [
                'required',
                'string',
                'max:100',
                'min:5',
                'unique:' . ($upgrade === 'vendor' ? 'vendors' : 'resellers') . ',shop_name_en',
            ],
            'phone' => [
                'required',
                'max:11',
                'min:10',
                'unique:' . ($upgrade === 'vendor' ? 'vendors' : 'resellers') . ',phone',
            ],
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'email' => [
                'required',
                'email',
                'unique:' . ($upgrade === 'vendor' ? 'vendors' : 'resellers') . ',email',
            ],
            'country' => ['required', 'string'],
            'district' => ['required', 'string'],
            'upozila' => ['required', 'string'],
            'village' => ['required', 'string'],
            'zip' => ['required', 'integer'],
            'road_no' => ['required'],
            'house_no' => ['required'],
            'address' => ['required'],
            'description' => ['nullable', 'string'],
        ];

        $validated = $request->validate($baseRules);

        $payload = array_merge($validated, [
            'slug' => Str::slug($validated['shop_name_en']),
            'logo' => $this->handleImageUpload($request->file('logo'), 'shop-logo', ''),
            'banner' => $this->handleImageUpload($request->file('banner'), 'shop-banner', ''),
        ]);

        if ($upgrade === 'vendor') {
            $payload['fixed_amount'] = 500;
            $record = vendor::create($payload);
        } else {
            $payload['fixed_amount'] = 500;
            $record = reseller::create($payload);
        }

        return redirect()->route('upgrade.vendor.edit', [
            'upgrade' => $upgrade,
            'id' => $record->id,
            'nav' => 'document',
        ]);
    }
}

