<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use App\Models\ta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class GeolocationController extends Controller
{
    public function indexReact(): Response
    {
        return Inertia::render('Auth/system/geolocation/index');
    }

    public function countriesReact(Request $request): Response
    {
        $countries = country::query()
            ->withCount('states')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Auth/system/geolocation/Countries', [
            'countries' => [
                'data' => $countries->getCollection()->map(function (country $item, int $index) use ($countries) {
                    return [
                        'sl' => ($countries->total() - ($countries->firstItem() + $index - 1)) . '.',
                        'id' => $item->id,
                        'name' => $item->name,
                        'states_count' => $item->states_count,
                        'iso2' => $item->iso2,
                        'iso3' => $item->iso3,
                    ];
                })->values()->all(),
                'links' => $countries->linkCollection()->toArray(),
            ],
        ]);
    }

    public function storeCountry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'iso2' => 'required|string|max:2|unique:countries,iso2',
            'iso3' => 'required|string|max:3|unique:countries,iso3',
        ]);

        country::create([
            'name' => $validated['name'],
            'iso2' => strtoupper($validated['iso2']),
            'iso3' => strtoupper($validated['iso3']),
        ]);

        return redirect()->route('system.geolocations.countries')->with('success', 'Country added successfully');
    }

    public function updateCountry(Request $request, country $country): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'iso2' => 'required|string|max:2|unique:countries,iso2,' . $country->id,
            'iso3' => 'required|string|max:3|unique:countries,iso3,' . $country->id,
        ]);

        $country->update([
            'name' => $validated['name'],
            'iso2' => strtoupper($validated['iso2']),
            'iso3' => strtoupper($validated['iso3']),
        ]);

        return redirect()->route('system.geolocations.countries')->with('success', 'Country updated successfully.');
    }

    public function destroyCountry(country $country): RedirectResponse
    {
        $country->delete();

        return redirect()->route('system.geolocations.countries')->with('success', 'Country deleted successfully.');
    }

    public function statesReact(Request $request): Response
    {
        $states = state::query()
            ->with('country')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Auth/system/geolocation/States', [
            'states' => [
                'data' => $states->getCollection()->map(function (state $item, int $index) use ($states) {
                    return [
                        'sl' => ($states->total() - ($states->firstItem() + $index - 1)) . '.',
                        'id' => $item->id,
                        'name' => $item->name,
                        'country_id' => $item->country_id,
                        'country_name' => $item->country?->name ?? 'N/A',
                        'country_code' => $item->country_code,
                        'iso2' => $item->iso2,
                        'iso3166_2' => $item->iso3166_2,
                    ];
                })->values()->all(),
                'links' => $states->linkCollection()->toArray(),
            ],
            'countries' => country::query()
                ->orderBy('name')
                ->get()
                ->map(function (country $item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'iso2' => $item->iso2,
                    ];
                })->values()->all(),
        ]);
    }

    public function storeState(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'country_id' => 'required|numeric',
            'country_code' => 'required|string|max:6',
            'iso2' => 'required|string|max:2|unique:states,iso2',
            'iso3166_2' => 'required|string|max:3|unique:states,iso3166_2',
        ]);

        state::create([
            'name' => $validated['name'],
            'iso2' => strtoupper($validated['iso2']),
            'iso3166_2' => strtoupper($validated['iso3166_2']),
            'country_id' => $validated['country_id'],
            'country_code' => strtoupper($validated['country_code']),
        ]);

        return redirect()->route('system.geolocations.states')->with('success', 'State added successfully');
    }

    public function updateState(Request $request, state $state): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'country_id' => 'required|numeric',
            'country_code' => 'required|string|max:6',
            'iso2' => 'required|string|max:2|unique:states,iso2,' . $state->id,
            'iso3166_2' => 'required|string|max:3|unique:states,iso3166_2,' . $state->id,
        ]);

        $state->update([
            'name' => $validated['name'],
            'iso2' => strtoupper($validated['iso2']),
            'iso3166_2' => strtoupper($validated['iso3166_2']),
            'country_id' => $validated['country_id'],
            'country_code' => strtoupper($validated['country_code']),
        ]);

        return redirect()->route('system.geolocations.states')->with('success', 'State updated successfully.');
    }

    public function destroyState(state $state): RedirectResponse
    {
        $state->delete();

        return redirect()->route('system.geolocations.states')->with('success', 'State deleted successfully.');
    }

    public function citiesReact(Request $request): Response
    {
        $countries = country::query()
            ->orderBy('name')
            ->get()
            ->map(function (country $item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                ];
            })->values()->all();

        $selectedCountry = $request->query('country');

        if (!$selectedCountry) {
            $selectedCountry = country::query()->value('id');
        }

        $selectedState = $request->query('state_id');

        $states = state::query()
            ->where('country_id', $selectedCountry)
            ->get()
            ->map(function (state $item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                ];
            })->values()->all();

        $cities = city::query()
            ->when($selectedState, fn ($query) => $query->where('state_id', $selectedState))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Auth/system/geolocation/Cities', [
            'filters' => [
                'country' => $selectedCountry ? (string) $selectedCountry : '',
                'state_id' => $selectedState ? (string) $selectedState : '',
            ],
            'countries' => $countries,
            'states' => $states,
            'cities' => [
                'data' => $cities->getCollection()->map(function (city $item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                })->values()->all(),
                'links' => $cities->linkCollection()->toArray(),
            ],
        ]);
    }

    public function storeCity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'country' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_name' => 'required|string|max:255',
        ]);

        city::create([
            'country_id' => $validated['country'],
            'state_id' => $validated['state_id'],
            'name' => $validated['city_name'],
        ]);

        return redirect()->route('system.geolocations.cities', [
            'country' => $validated['country'],
            'state_id' => $validated['state_id'],
        ])->with('success', 'City Added');
    }

    public function destroyCity(Request $request, city $city): RedirectResponse
    {
        $city->delete();

        return redirect()->route('system.geolocations.cities', [
            'country' => $request->input('country', $request->query('country')),
            'state_id' => $request->input('state_id', $request->query('state_id')),
        ])->with('success', 'City Deleted');
    }

    public function areaReact(Request $request): Response
    {
        $selectedCountry = $request->query('country');
        $selectedState = $request->query('state_id');
        $selectedCity = $request->query('city_id');

        $countries = country::query()
            ->orderBy('name')
            ->get()
            ->map(fn (country $item) => [
                'id' => $item->id,
                'name' => $item->name,
            ])->values()->all();

        $states = $selectedCountry
            ? state::query()
                ->where('country_id', $selectedCountry)
                ->orderBy('name')
                ->get()
                ->map(fn (state $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])->values()->all()
            : [];

        $cities = $selectedState
            ? city::query()
                ->where('state_id', $selectedState)
                ->orderBy('name')
                ->get()
                ->map(fn (city $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])->values()->all()
            : [];

        $areas = ta::query()
            ->when($selectedCity, fn ($query) => $query->where('city_id', $selectedCity))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Auth/system/geolocation/Area', [
            'filters' => [
                'country' => $selectedCountry ? (string) $selectedCountry : '',
                'state_id' => $selectedState ? (string) $selectedState : '',
                'city_id' => $selectedCity ? (string) $selectedCity : '',
            ],
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'areas' => [
                'data' => $areas->getCollection()->map(fn (ta $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])->values()->all(),
                'links' => $areas->linkCollection()->toArray(),
            ],
        ]);
    }

    public function storeArea(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'area_name' => 'required|string|max:255',
        ]);

        ta::create([
            'city_id' => $validated['city_id'],
            'name' => $validated['area_name'],
            'slug' => Str::slug($validated['area_name']),
        ]);

        return redirect()->route('system.geolocations.area', [
            'country' => $request->input('country', $request->query('country')),
            'state_id' => $request->input('state_id', $request->query('state_id')),
            'city_id' => $validated['city_id'],
        ])->with('success', 'Area Added');
    }

    public function destroyArea(Request $request, ta $area): RedirectResponse
    {
        $area->delete();

        return redirect()->route('system.geolocations.area', [
            'country' => $request->input('country', $request->query('country')),
            'state_id' => $request->input('state_id', $request->query('state_id')),
            'city_id' => $request->input('city_id', $request->query('city_id')),
        ])->with('success', 'Area Deleted');
    }
}
