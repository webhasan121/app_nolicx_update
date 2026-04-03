<?php

use App\Models\User;
use App\Models\user_has_refs as uref;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\city;
use App\Models\country;
use App\Models\state;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $phone = '';
    public $reference = null;

    public $country_id = null;
    public $state_id = null;
    public $city_id = null;

    public $countries = [];
    public $states = [];
    public $cities = [];

    public function mount()
    {
        $this->countries = country::orderBy('name')->get();
    }

    public function updatedCountryId()
    {
        $this->state_id = null;
        $this->city_id = null;

        $this->states = [];
        $this->cities = [];

        if ($this->country_id) {
            $this->states = state::where('country_id', $this->country_id)->orderBy('name')->get();
        }
    }

    public function updatedStateId()
    {
        $this->city_id = null;
        $this->cities = [];

        if ($this->state_id) {
            $this->cities = city::where('state_id', $this->state_id)->orderBy('name')->get();
        }
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',

            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
            'city_id'    => 'nullable|exists:cities,id',

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $country = country::find($this->country_id);

        $validated['country_code']  = $country->iso2 ?? null;
        $validated['currency']      = $country->currency ?? 'USD';
        $validated['currency_sing'] = $country->currency_symbol ?? '$';

        // Reference handling
        if (!empty($this->reference) && uref::where('ref', $this->reference)->exists()) {
            $validated['reference'] = $this->reference;
            $validated['reference_accepted_at'] = now();
        } else {
            $validated['reference'] = config('app.ref');
        }

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard'), navigate: true);
    }
};

?>

<section class="p-8 bg-white rounded-md lg:w-2/5" style="max-width:800px">
    <form wire:submit="register" >
      <div class="relative" >
        <x-input-label value="Name" />
        <x-text-input wire:model="name" class="w-full mt-1" />
        <x-input-error :messages="$errors->get('name')" />
      </div>
      <div class="relative" >
        <x-input-label value="Email" class="mt-4" />
        <x-text-input wire:model="email" class="w-full mt-1" />
        <x-input-error :messages="$errors->get('email')" />
      </div>

      <div class="grid gap-4 mt-4 lg:grid-cols-2" >
        <div class="relative" >
          <x-input-label value="Password" />
          <x-text-input wire:model="password" id="password" type="password" class="w-full mt-1" />
          <button type="button" class="absolute flex items-center text-gray-500 top-9 right-2" onclick="togglePassword('password', this)" >
            <i class="fas fa-eye"></i>
          </button>
          <x-input-error :messages="$errors->get('password')" />
        </div>
        <div class="relative" >
          <x-input-label value="Confirm Password" />
          <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password" class="w-full mt-1" />
          <button type="button" class="absolute flex items-center text-gray-500 top-9 right-2" onclick="togglePassword('password_confirmation', this)" >
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div class="relative" >
          <x-input-label value="Phone" />
          <x-text-input wire:model="phone" class="w-full mt-1" />
          <x-input-error :messages="$errors->get('phone')" />
        </div>
        <div class="relative" >
          <x-input-label value="Reference (optional)" />
          <x-text-input wire:model="reference" class="w-full mt-1" />
        </div>
        <div class="relative" >
          <x-input-label value="Country" />
          <select wire:model.live="country_id" class="w-full mt-1 rounded-md">
            <option value="">{{ __('-- Select Country --') }}</option>
            @foreach ($countries as $country)
              <option value="{{ $country->id }}">{{ $country->name }}</option>
            @endforeach
          </select>
          <x-input-error :messages="$errors->get('country_id')" />
        </div>
        <div class="relative" >
          <x-input-label value="State / District" />
          <select wire:model.live="state_id" class="w-full mt-1 rounded-md">
            <option value="">{{ __('-- Select State --') }}</option>
            @foreach ($states as $state)
              <option value="{{ $state->id }}">{{ $state->name }}</option>
            @endforeach
          </select>
          <x-input-error :messages="$errors->get('state_id')" />
        </div>
      </div>

      <div class="flex items-center justify-between mt-6" >
        <div class="text-left" >
          <p>{{ __('Already have an account!') }}</p>
          <x-nav-link href="{{ route('login') }}" >{{ __('Login') }}</x-nav-link>
        </div>
        <x-primary-button>{{ __('Register') }}</x-primary-button>
      </div>
    </form>
</section>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
