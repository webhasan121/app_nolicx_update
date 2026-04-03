<?php

namespace App\Livewire\System\Geolocation;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\country;
use App\Models\state;

#[layout('layouts.app')]
class States extends Component
{
    use WithPagination;

    public $name, $iso2, $iso3166_2, $country_id, $country_code, $stateId;
    public $isEdit = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:150',
        'country_id' => 'required|numeric',
        'country_code' => 'required|string|max:6',
        'iso2' => 'required|string|max:2|unique:states,iso2',
        'iso3166_2' => 'required|string|max:3|unique:states,iso3166_2'
    ];

    protected $listeners = ['refresh' => '$refresh'];

    // Reset fields & open modal for create
    public function create()
    {
        $this->reset(['name', 'iso2', 'iso3166_2', 'country_id', 'country_code', 'stateId', 'isEdit']);
        $this->dispatch('open-modal', 'stateModal');
    }

    // Auto-fill ISO2 when a country is selected
    public function updatedCountryId($value)
    {
        $country = Country::find($value);
        $this->country_code = $country ? $country->iso2 : '';
    }

    // Store new state
    public function store()
    {
        $this->validate();

        State::create([
            'name' => $this->name,
            'iso2' => strtoupper($this->iso2),
            'iso3166_2' => strtoupper($this->iso3166_2),
            'country_id' => $this->country_id,
            'country_code' => strtoupper($this->country_code),
        ]);

        $this->dispatch('success', 'State added successfully');
        $this->reset(['name', 'iso2', 'iso3166_2', 'country_id', 'country_code']);
        $this->dispatch('close-modal', 'stateModal');
    }

    // Load state for editing
    public function edit($id)
    {
        $state = State::findOrFail($id);

        $this->stateId = $state->id;
        $this->name = $state->name;
        $this->iso2 = $state->iso2;
        $this->iso3166_2 = $state->iso3166_2;
        $this->country_id = $state->country_id;
        $this->country_code = $state->country_code;
        $this->isEdit = true;

        $this->dispatch('open-modal', 'stateModal');
    }

    // Update existing state
    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'country_id' => 'required|numeric',
            'country_code' => 'required|string|max:6',
            'iso2' => 'required|string|max:2|unique:states,iso2,' . $this->stateId,
            'iso3166_2' => 'required|string|max:3|unique:states,iso3166_2,' . $this->stateId,
        ]);

        State::where('id', $this->stateId)->update([
            'name' => $this->name,
            'iso2' => strtoupper($this->iso2),
            'iso3166_2' => strtoupper($this->iso3166_2),
            'country_id' => $this->country_id,
            'country_code' => strtoupper($this->country_code),
        ]);

        $this->dispatch('success', 'State updated successfully.');
        $this->reset(['name', 'iso2', 'iso3166_2', 'country_id', 'country_code', 'stateId', 'isEdit']);
        $this->dispatch('close-modal', 'stateModal');
    }

    // Delete state
    public function delete($id)
    {
        State::findOrFail($id)->delete();
        $this->dispatch('success', 'State deleted successfully.');
    }

    // Render with states & countries
    public function render()
    {
        $states = State::where('name', 'like', '%'.$this->search.'%')
            ->latest('id')
            ->paginate(20);

        $countries = Country::orderBy('name')->get();

        return view('livewire.system.geolocation.states', compact('states', 'countries'));
    }
}
