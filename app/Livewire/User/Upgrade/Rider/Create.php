<?php

namespace App\Livewire\User\Upgrade\Rider;

use App\Models\rider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\HandleImageUpload;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use App\Models\ta;
use Illuminate\Support\Facades\Log;

#[layout('layouts.user.dash.userDash')]
class Create extends Component
{
    use WithFileUploads, HandleImageUpload;

    public $phone, $email, $nid, $nid_photo_front, $nid_photo_back, $fixed_address, $current_address, $area_condition, $targeted_area, $vehicle_type, $vehicle_number, $vehicle_model, $vehicle_color;
    public  $state_name, $city_name, $area_name, $country;

    public function mount()
    {
        if (auth()->user()?->requestsToBeRider()?->pending()->exists() || auth()->user()?->requestsToBeRider()?->active()->exists()) {
            Session::flash('warning', 'You have another unprocessable request !');
            $this->dispatch('alert', 'You have another unprocessable request !');
            $this->redirectIntended(route('upgrade.rider.index'), true);
        }

        $this->phone = auth()->user()->phone;
        $this->email = auth()->user()->email;
        $this->state_name = auth()->user()->state;
        $this->city_name = auth()->user()->city;
    }

    public function store()
    {
        $validData = $this->validate([
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'nid' => 'required|string',
            'nid_photo_front' => 'required|mimes:jpg,jpeg,png| max:1024',
            'nid_photo_back' => 'required|mimes:jpg,jpeg,png| max:1024',
            'vehicle_type' => 'required|string',
            'vehicle_number' => 'required',
            'vehicle_model' => 'required',
            'vehicle_color' => 'required',
            'fixed_address' => 'required',
            'current_address' => 'required',
            'state_name' => 'required',
            'city_name' => 'required',
            'area_name' => 'required',
        ]);

        // array_merge($validData)
        try {

            rider::create([
                'user_id' => Auth::id(),
                'phone' => $validData['phone'],
                'email' => $validData['email'],
                'nid' => $validData['nid'],
                'nid_photo_front' => $this->handleImageUpload($this->nid_photo_front, 'rider', null),
                'nid_photo_back' => $this->handleImageUpload($this->nid_photo_back, 'rider', null),
                'fixed_address' => $validData['fixed_address'],
                'current_address' => $validData['current_address'],
                'targeted_area' => $this->area_name,
                'area_condition' => $this->area_condition,
                'vehicle_type' => $this->vehicle_type,
                'vehicle_number' => $this->vehicle_number,
                'vehicle_model' => $this->vehicle_model,
                'vehicle_color' => $this->vehicle_color,

                'country' => $this->country,
                'district' => $this->state_name,
            ]);
            // rider::created($validData);
            $this->redirectIntended(route('upgrade.rider.index'), true);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
            $this->dispatch('error', 'Have an error. Please fill all the required fill');
        }
    }

    private function processImageStore($image, $targetStoreName)
    {
        //
        $targetPath = 'rider-document';
        if ($image) {
            $ext = $image->getClientOriginalExtension();
            $name = "$targetStoreName" . time() . ".$ext";
            // $filePath = $image->move(public_path($targetStorePath), $name);
            $image->storeAs($targetPath, $name, 'public');

            return $name;
        }
    }

    public function updated($property)
    {
        // dd($property);
        // if ($property == 'state_name') {
        //     $this->cities = city::where('state_id', state::where('name', $this->state_name)->first()?->id)->get();
        // }

        // if ($property == 'city_name') {
        //     $this->area = ta::where('city_id', city::where('name', $this->city_name)->first()?->id)->get();
        // }
    }



    public function render()
    {

        $city = [];
        $area = [];
        if ($this->state_name) {
            $city = city::where('state_id', state::where('name', $this->state_name)->first()?->id)->get();
        }
        if ($this->city_name) {
            $area = ta::where('city_id', city::where('name', $this->city_name)->first()?->id)->get();
        }

        // return state::where('country_id', country::where('name', 'Bangladesh')->first()?->id)->get('id');
        return view(
            'livewire.user.upgrade.rider.create',
            [
                'states' => state::where('country_id', country::where('name', 'Bangladesh')->first()?->id)->get(),
                'cities' => $city,
                'area' => $area,
            ]
        );
    }
}
