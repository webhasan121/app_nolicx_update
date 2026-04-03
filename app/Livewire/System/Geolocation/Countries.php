<?php

namespace App\Livewire\System\Geolocation;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\country;

#[layout('layouts.app')]
class Countries extends Component {
    use WithPagination;

    public $name, $iso2, $iso3, $countryId;
    public $isEdit = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:150',
        'iso2' => 'required|string|max:2|unique:countries,iso2',
        'iso3' => 'required|string|max:3|unique:countries,iso3'
    ];

    protected $listeners = ['refresh' => '$refresh'];

    public function create()
    {
        $this->reset(['name', 'iso2', 'iso3', 'countryId', 'isEdit']);
        $this->dispatch('open-modal', 'countryModal');
    }

    public function store() {
        $this->validate();
        country::create([
            'name' => $this->name,
            'iso2' => strtoupper($this->iso2),
            'iso3' => strtoupper($this->iso3),
        ]);
        $this->dispatch('refresh');
        $this->reset(['name', 'iso2', 'iso3']);
        $this->dispatch('success', 'Country added successfully');
        $this->dispatch('close-modal', 'countryModal');
    }

    public function edit($id) {
        $country = country::findOrFail($id);

        $this->countryId = $country->id;
        $this->name = $country->name;
        $this->iso2 = $country->iso2;
        $this->iso3 = $country->iso3;
        $this->isEdit = true;

        $this->dispatch('open-modal', 'countryModal');
    }

    public function update() {
        $this->validate([
            'name' => 'required|string|max:150',
            'iso2' => 'required|string|max:2|unique:countries,iso2,' . $this->countryId,
            'iso3' => 'required|string|max:3|unique:countries,iso3,' . $this->countryId,
        ]);

        country::where('id', $this->countryId)->update([
            'name' => $this->name,
            'iso2' => strtoupper($this->iso2),
            'iso3' => strtoupper($this->iso3),
        ]);

        $this->dispatch('success', 'Country updated successfully.');
        $this->reset(['name', 'iso2', 'iso3', 'countryId', 'isEdit']);
        $this->dispatch('close-modal', 'countryModal');
    }

    public function delete($id) {
        country::findOrFail($id)->delete();
        $this->dispatch('success', 'Country deleted successfully.');
    }

    public function render() {
        $countries = country::where('name', 'like', '%'.$this->search.'%')->latest('id')->paginate(20);
        return view('livewire.system.geolocation.countries', get_defined_vars());
    }
}
