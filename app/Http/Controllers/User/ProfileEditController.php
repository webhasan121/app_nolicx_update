<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileEditController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $countries = country::orderBy('name')->get(['id', 'name']);

        $countryId = $this->resolveCountryId($user->country);
        $stateId = $this->resolveStateId($user->state, $countryId);
        $cityId = $this->resolveCityId($user->city, $stateId);

        return Inertia::render('User/Profile/Edit', [
            'userProfile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'bio' => $user->bio,
                'dob' => $user->dob,
                'gender' => $user->gender,
                'country' => $countryId,
                'state' => $stateId,
                'city' => $cityId,
                'targeted_area' => $user->requestsToBeRider()->where('status', 'Active')->value('targeted_area'),
                'line1' => $user->line1,
                'line2' => $user->line2,
                'zip' => $user->zip,
                'email_verified' => $user->hasVerifiedEmail(),
                'must_verify_email' => $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
            ],
            'countries' => $countries,
            'states' => $countryId ? state::where('country_id', $countryId)->orderBy('name')->get(['id', 'name']) : [],
            'cities' => $stateId ? city::where('state_id', $stateId)->orderBy('name')->get(['id', 'name']) : [],
            'genders' => [
                'Male' => 'Male',
                'Female' => 'Female',
                'Other' => 'Other',
            ],
            'passwordRules' => [
                'At least 8 characters',
                'At least one uppercase letter',
                'At least one lowercase letter',
                'At least one number',
                'At least one special character (!@#$%^&*)',
            ],
        ]);
    }

    protected function resolveCountryId($value)
    {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return country::whereRaw('LOWER(name) = ?', [strtolower($value)])->value('id');
    }

    protected function resolveStateId($value, $countryId = null)
    {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return state::when($countryId, fn ($query) => $query->where('country_id', $countryId))
            ->whereRaw('LOWER(name) = ?', [strtolower($value)])
            ->value('id');
    }

    protected function resolveCityId($value, $stateId = null)
    {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return city::when($stateId, fn ($query) => $query->where('state_id', $stateId))
            ->whereRaw('LOWER(name) = ?', [strtolower($value)])
            ->value('id');
    }

    public function loadStates($country)
    {
        return response()->json(
            state::where('country_id', $country)->orderBy('name')->get(['id', 'name'])
        );
    }

    public function loadCities($state)
    {
        return response()->json(
            city::where('state_id', $state)->orderBy('name')->get(['id', 'name'])
        );
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:250'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'country' => ['nullable', 'exists:countries,id'],
            'state' => ['nullable', 'exists:states,id'],
            'city' => ['nullable', 'exists:cities,id'],
            'targeted_area' => ['nullable', 'string', 'max:255'],
            'line1' => ['nullable', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:20'],
        ]);

        $countryName = $validated['country'] ? country::find($validated['country'])?->name : null;
        $stateName = $validated['state'] ? state::find($validated['state'])?->name : null;
        $cityName = $validated['city'] ? city::find($validated['city'])?->name : null;

        $profileData = $validated;
        unset($profileData['targeted_area']);
        $profileData['country'] = $countryName;
        $profileData['state'] = $stateName;
        $profileData['city'] = $cityName;

        $user->fill($profileData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($user->requestsToBeRider()->where('status', 'Active')->exists()) {
            $user->requestsToBeRider()->where('status', 'Active')->update([
                'phone' => $validated['phone'] ?? $user->phone,
                'email' => $validated['email'] ?? $user->email,
                'country' => $countryName,
                'district' => $stateName,
                'city' => $cityName,
                'targeted_area' => $validated['targeted_area'] ?: $cityName,
            ]);
        }

        $user->save();

        return redirect()->back()->with('success', 'Saved.');
    }

    public function sendVerification(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $user->sendEmailVerificationNotification();

        return redirect()->back()->with('success', 'A new verification link has been sent to your email address.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password updated.');
    }
}
