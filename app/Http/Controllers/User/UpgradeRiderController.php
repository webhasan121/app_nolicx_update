<?php

namespace App\Http\Controllers\User;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\country;
use App\Models\rider;
use App\Models\state;
use App\Models\ta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UpgradeRiderController extends Controller
{
    use HandleImageUpload;

    public function index(Request $request)
    {
        $riderRequests = $request->user()
            ->requestsToBeRider()
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'phone' => $item->phone,
                    'targeted_area' => Str::upper((string) $item->targeted_area),
                    'created_at' => Carbon::parse($item->created_at)->format('d M Y'),
                    'status' => $item->status,
                    'is_rejected' => (bool) $item->is_rejected,
                ];
            });

        return Inertia::render('User/Upgrade/Rider/Index', [
            'rider' => $riderRequests,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user?->requestsToBeRider()?->pending()->exists() || $user?->requestsToBeRider()?->active()->exists()) {
            return redirect()
                ->route('upgrade.rider.index')
                ->with('warning', 'You have another unprocessable request !');
        }

        $countryId = country::where('name', 'Bangladesh')->value('id');

        return Inertia::render('User/Upgrade/Rider/Create', [
            'defaults' => [
                'phone' => $user->phone,
                'email' => $user->email,
                'state_name' => $user->state,
                'city_name' => $user->city,
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

    public function loadAreas($city)
    {
        return response()->json(
            ta::where('city_id', $city)
                ->orderBy('name')
                ->get(['id', 'name', 'city_id'])
        );
    }

    public function store(Request $request)
    {

    // echo '<pre>';
    // print_r($request->all());
    // echo '</pre>';
    // exit();
        $user = $request->user();
        if ($user?->requestsToBeRider()?->pending()->exists() || $user?->requestsToBeRider()?->active()->exists()) {
            return redirect()
                ->route('upgrade.rider.index')
                ->with('warning', 'You have another unprocessable request !');
        }

        $validData = $request->validate([
            'phone' => ['required', 'numeric'],
            'email' => ['required', 'email'],
            'nid' => ['required', 'string'],
            'nid_photo_front' => ['required', 'mimes:jpg,jpeg,png', 'max:1024'],
            'nid_photo_back' => ['required', 'mimes:jpg,jpeg,png', 'max:1024'],
            'vehicle_type' => ['required', 'string'],
            'vehicle_number' => ['required'],
            'vehicle_model' => ['required'],
            'vehicle_color' => ['required'],
            'fixed_address' => ['required'],
            'current_address' => ['required'],
            'state_name' => ['required'],
            'city_name' => ['required'],
            'area_name' => ['required'],
            'area_condition' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
        ]);

        rider::create([
            'user_id' => $user->id,
            'phone' => $validData['phone'],
            'email' => $validData['email'],
            'nid' => $validData['nid'],
            'nid_photo_front' => $this->handleImageUpload($request->file('nid_photo_front'), 'rider', null),
            'nid_photo_back' => $this->handleImageUpload($request->file('nid_photo_back'), 'rider', null),
            'fixed_address' => $validData['fixed_address'],
            'current_address' => $validData['current_address'],
            'targeted_area' => $validData['area_name'],
            'area_condition' => $validData['area_condition'] ?? null,
            'vehicle_type' => $validData['vehicle_type'],
            'vehicle_number' => $validData['vehicle_number'],
            'vehicle_model' => $validData['vehicle_model'],
            'vehicle_color' => $validData['vehicle_color'],
            'country' => $validData['country'] ?? 'Bangladesh',
            'district' => $validData['state_name'],
        ]);

        return redirect()
            ->route('upgrade.rider.index')
            ->with('success', 'Rider request submitted successfully.');
    }

    public function edit(Request $request, $id)
    {
        $data = $request->user()->requestsToBeRider()->find($id);
        if (!$data || $data->status !== 'Pending') {
            return redirect()->back()->with('error', 'Unable to edit or update');
        }

        return Inertia::render('User/Upgrade/Rider/Edit', [
            'rider' => [
                'id' => $data->id,
                'phone' => $data->phone,
                'email' => $data->email,
                'nid' => $data->nid,
                'fixed_address' => $data->fixed_address,
                'current_address' => $data->current_address,
                'area_condition' => $data->area_condition,
                'targeted_area' => $data->targeted_area,
                'nid_photo_front' => $data->nid_photo_front,
                'nid_photo_back' => $data->nid_photo_back,
                'nid_photo_front_url' => $data->nid_photo_front ? asset('storage/' . $data->nid_photo_front) : null,
                'nid_photo_back_url' => $data->nid_photo_back ? asset('storage/' . $data->nid_photo_back) : null,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->user()->requestsToBeRider()->find($id);
        if (!$data || $data->status !== 'Pending') {
            return redirect()->back()->with('warning', 'Unable to Edit or Update');
        }

        $validData = $request->validate([
            'phone' => ['required', 'numeric'],
            'email' => ['required', 'email'],
            'nid' => ['required', 'string'],
            'fixed_address' => ['required', 'string'],
            'current_address' => ['required', 'string'],
            'area_condition' => ['required', 'string'],
            'targeted_area' => ['required', 'string'],
            'nid_photo_front' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'nid_photo_back' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $updateData = [
            'phone' => $validData['phone'],
            'email' => $validData['email'],
            'nid' => $validData['nid'],
            'fixed_address' => $validData['fixed_address'],
            'current_address' => $validData['current_address'],
            'area_condition' => $validData['area_condition'],
            'targeted_area' => $validData['targeted_area'],
            'nid_photo_front' => $this->handleImageUpload($request->file('nid_photo_front'), 'rider-document', $data->nid_photo_front),
            'nid_photo_back' => $this->handleImageUpload($request->file('nid_photo_back'), 'rider-document', $data->nid_photo_back),
        ];

        $data->update($updateData);

        return redirect()->back()->with('success', 'Information Updated Successfully');
    }
}
