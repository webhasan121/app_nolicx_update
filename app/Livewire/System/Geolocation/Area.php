<?php

namespace App\Livewire\System\Geolocation;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\country;
use App\Models\city;
use App\Models\state;
use App\Models\ta;
use Illuminate\Support\Str;

#[layout('layouts.app')]
class Area extends Component
{
    use WithPagination;

    #[URL]
    public $country = null;
    public $state_id = null;
    public $city_id = null;

    public $city_name;
    public $area_name;

    protected $queryString = ['country', 'state_id', 'city_id'];

    protected $listeners = ['refresh' => '$refresh'];

    /* Reset child dropdowns when parent changes */
    public function updatedCountry()
    {
        $this->reset(['state_id', 'city_id']);
    }

    public function updatedStateId()
    {
        $this->reset(['city_id']);
    }

    public function saveCity()
    {
        try {

            //code...
            // add new city
            $this->validate([
                'state_id' => 'required|exists:states,id',
                'city_name' => 'required|string|max:255',
            ]);
            city::create([
                'state_id' => $this->state_id,
                'name' => $this->city_name,
            ]);

            // reset the form
            $this->dispatch('refresh');
            $this->reset(['city_name']);
            $this->dispatch('success', 'City Added');
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('error', 'Error Adding City: ' . $th->getMessage());
        }
    }

    public function newArea()
    {
        try {

            //code...
            // add new city
            $this->validate([
                'city_id' => 'required',
                'area_name' => 'required|string|max:255',
            ]);
            ta::create([
                'city_id' => $this->city_id,
                'name' => $this->area_name,
                'slug' => Str::slug($this->area_name)
            ]);

            // reset the form
            $this->dispatch('refresh');
            $this->reset(['area_name']);
            $this->dispatch('success', 'Area Added');
            $this->dispatch('close-modal', 'newAreaModal');
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('error', 'Error Adding Area: ' . $th->getMessage());
        }
    }

    public function deleteCity($cityId)
    {
        try {
            //code...
            $city = ta::findOrFail($cityId);
            $city->delete();
            $this->dispatch('refresh');
            $this->dispatch('success', 'Area Deleted');
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('error', 'Error Deleting City: ' . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.system.geolocation.area', [
            'countries' => Country::orderBy('name')->get(),
            'states' => $this->country
                ? State::where('country_id', $this->country)->orderBy('name')->get()
                : [],
            'cities' => $this->state_id
                ? City::where('state_id', $this->state_id)->orderBy('name')->get()
                : [],
            'areas' => $this->city_id
                ? Ta::where('city_id', $this->city_id)->latest()->paginate(20)
                : Ta::latest()->paginate(20),
        ]);
    }
}
