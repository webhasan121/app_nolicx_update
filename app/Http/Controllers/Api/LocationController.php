<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function countries(Request $request)
    {
        $query = country::query()
            ->select('id', 'name', 'iso2', 'phonecode')
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = trim((string) $request->input('search'));
                $builder->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name');

        return $this->paginated($query, $request, 'Countries fetched');
    }

    public function states(Request $request)
    {
        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = state::query()
            ->select('id', 'name', 'country_id')
            ->where('country_id', $validated['country_id'])
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = trim((string) $request->input('search'));
                $builder->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name');

        return $this->paginated($query, $request, 'States fetched');
    }

    public function cities(Request $request)
    {
        $validated = $request->validate([
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = city::query()
            ->select('id', 'name', 'state_id')
            ->where('state_id', $validated['state_id'])
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = trim((string) $request->input('search'));
                $builder->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name');

        return $this->paginated($query, $request, 'Cities fetched');
    }

    private function paginated($query, Request $request, string $message)
    {
        $perPage = max(1, min((int) $request->input('per_page', 20), 100));
        $items = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $items->items(),
        ]);
    }
}
