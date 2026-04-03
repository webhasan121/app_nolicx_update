<?php

namespace App\Livewire\System\Geolocation;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\country;
use App\Models\state;
use App\Models\city;
use Illuminate\Support\Facades\Artisan;
use Livewire\WithPagination;

#[layout('layouts.app')]
class Cities extends Component
{
    use WithPagination;

    #[Url]
    public $country = null;

    #[Url]
    public $state_id = null;

    public $city_name;

    protected $listeners = ['refresh' => '$refresh'];

    public function mount()
    {
        if (!$this->country) {
            $this->country = Country::value('id'); // first country
        }
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
                'country_id' => $this->country,
                'state_id' => $this->state_id,
                'name' => $this->city_name,
            ]);

            // reset the form
            $this->dispatch('refresh');
            $this->reset(['city_name']);
            $this->dispatch('success', 'City Added');
            $this->dispatch('close-modal', 'newCityModal');
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('error', 'Error Adding City: ' . $th->getMessage());
        }
    }

    public function deleteCity($cityId)
    {
        try {
            //code...
            $city = city::findOrFail($cityId);
            $city->delete();
            $this->dispatch('refresh');
            $this->dispatch('success', 'City Deleted');
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('error', 'Error Deleting City: ' . $th->getMessage());
        }
    }

    public function updatedCountry()
    {
        $this->state_id = null;   // reset state when country changes
    }

    public function updatedStateId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $countries = country::get();
        $cq = city::query();
        $states = State::where('country_id', $this->country)->get();

        $cq->where(['state_id' => $this->state_id]);

        $cities = city::latest('id')->paginate(20);

        return view('livewire.system.geolocation.cities', [
            'countries' => $countries,
            // 'cities' => $cq->get(),
            'cities' => $cities,
            'state' => $states,
        ]);
    }
}
