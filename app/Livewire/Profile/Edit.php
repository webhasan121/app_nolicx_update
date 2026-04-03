<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Ta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;

#[Layout('layouts.user.dash.userDash')]
class Edit extends Component {
    public $name, $email, $phone, $bio, $dob, $gender, $country, $state, $city, $line1, $line2, $zip;

    public $genders = [];
    public $countries = [];
    public $states = [];
    public $cities = [];

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public $passwordRules = [];

    public function mount() {
        $user = Auth::user();

        // If you want only Bangladesh
        $this->countries = Country::where('name', 'Bangladesh')->get();

        // Load user basic data
        $this->name    = $user->name;
        $this->email    = $user->email;
        $this->phone    = $user->phone;
        $this->bio    = $user->bio;
        $this->dob    = $user->dob;
        $this->gender  = $user->gender;
        $this->country = $user->country;
        $this->state   = $user->state;
        $this->city    = $user->city;
        $this->line1    = $user->line1;
        $this->line2    = $user->line2;
        $this->zip    = $user->zip;

        // 🔥 Preload states if country exists
        // if ($this->country) {
        //     $this->states = State::where('country_id', $this->country)->get();
        // }
        $countryId = $this->resolveId(Country::class, $this->country);
        $this->states = $countryId ? State::where('country_id', $countryId)->get() : [];

        // 🔥 Preload cities if state exists
        // if ($this->state) {
        //     $this->cities = City::where('state_id', $this->state)->get();
        // }
        $stateId = $this->resolveId(State::class, $this->state);
        $this->cities = $stateId ? City::where('state_id', $stateId)->get() : [];

        // Gender options
        $this->genders = [
            'Male' => 'Male',
            'Female' => 'Female',
            'Other' => 'Other'
        ];

        // Password rules
        $this->passwordRules = [
            'At least 8 characters',
            'At least one uppercase letter',
            'At least one lowercase letter',
            'At least one number',
            'At least one special character (!@#$%^&*)'
        ];
    }

    // 🔹 Helper to resolve ID from int or string
    protected function resolveId($model, $value)
    {
        if (!$value) return null;

        if (is_numeric($value)) {
            return (int) $value;
        }

        $record = $model::where('name', $value)->first();
        return $record ? $record->id : null;
    }

    // 🔥 When Country Changes → Load States
    public function updatedCountry($countryId) {
        // $this->states = State::where('country_id', $countryId)->get();
        $this->states = State::where('country_id', $this->resolveId(Country::class, $countryId))->get();
        // reset lower levels
        $this->state = null;
        $this->cities = [];
        $this->city = null;
    }

    // 🔥 When State Changes → Load Cities
    public function updatedState($stateId) {
        // $this->cities = City::where('state_id', $stateId)->get();
        $this->cities = City::where('state_id', $this->resolveId(State::class, $stateId))->get();
        $this->city = null;
    }

    // 🔥 Update Profile
    public function updateProfile() {
        $user = Auth::user();

        $this->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'bio'     => 'nullable|string|max:250',
            'dob'     => 'nullable|date',
            'gender'  => 'nullable|in:Male,Female,Other',
            'country' => 'nullable|exists:countries,id',
            'state'   => 'nullable|exists:states,id',
            'city'    => 'nullable|exists:cities,id',
            'line1'   => 'nullable|string|max:255',
            'line2'   => 'nullable|string|max:255',
            'zip'     => 'nullable|string|max:20',
        ]);

        // Update all fields
        $user->name    = $this->name;
        $user->email   = $this->email;
        $user->phone   = $this->phone;
        $user->bio     = $this->bio;
        $user->dob     = $this->dob;
        $user->gender  = $this->gender;
        $user->country = $this->country;
        $user->state   = $this->state;
        $user->city    = $this->city;
        $user->line1   = $this->line1;
        $user->line2   = $this->line2;
        $user->zip     = $this->zip;

        // ✅ If email changed, reset verification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($user->requestsToBeRider()->where('status', 'Active')->exists()) {
            $user->requestsToBeRider()->where('status', 'Active')->update([
                'phone'     => $this->phone ?? $user->phone,
                'email'     => $this->email ?? $user->email,
                'country'   => $this->country ?? $user->country,
                'district'  => $this->state ?? $user->state,
                'city'      => $this->city ?? $user->city,
                'targeted_area' => $this->city,
            ]);
        }


        $user->save();

        // Optional: emit Livewire event if using <x-action-message>
        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
    }

    public function render() {
        return view('livewire.profile.edit');
    }
}
