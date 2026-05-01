<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return ApiResponse::success($this->profilePayload($request->user()), 'Profile fetched');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->merge([
            'country_id' => $request->input('country_id', $request->input('country')),
            'state_id' => $request->input('state_id', $request->input('state')),
            'city_id' => $request->input('city_id', $request->input('city')),
        ]);

        $countryId = $request->input('country_id');
        $stateId = $request->input('state_id');
        $cityId = $request->input('city_id');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25', Rule::unique('users', 'phone')->ignore($user->id)],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'bio' => ['nullable', 'string', 'max:250'],
            'line1' => ['nullable', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:20'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'state_id' => [
                'nullable',
                'integer',
                Rule::exists('states', 'id')->where('country_id', (int) $countryId),
            ],
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where('state_id', (int) $stateId),
            ],
        ]);

        $country = $countryId ? country::find($countryId) : null;
        $state = $stateId ? state::find($stateId) : null;
        $city = $cityId ? city::find($cityId) : null;

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'line1' => $validated['line1'] ?? null,
            'line2' => $validated['line2'] ?? null,
            'zip' => $validated['zip'] ?? null,
            'country' => $country?->name ?? $user->country,
            'country_code' => $country?->iso2 ?? $user->country_code,
            'state' => $state?->name ?? $user->state,
            'city' => $city?->name ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return ApiResponse::success($this->profilePayload($user->fresh()), 'Profile updated');
    }

    private function profilePayload($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'dob' => $user->dob,
            'gender' => $user->gender,
            'bio' => $user->bio,
            'line1' => $user->line1,
            'line2' => $user->line2,
            'zip' => $user->zip,
            'country' => $user->country,
            'country_code' => $user->country_code,
            'state' => $user->state,
            'city' => $user->city,
        ];
    }
}
