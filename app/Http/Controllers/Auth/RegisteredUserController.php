<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\UserHasRefs;
use App\Support\RegistrationIdentityGuard;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    public function store_user(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', RegistrationIdentityGuard::uniqueEmailAliasRule()],
            'phone' => ['required', 'string', 'max:25', RegistrationIdentityGuard::uniquePhoneRule()],

            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
            'city_id'    => 'nullable|exists:cities,id',

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $country = country::find($validated['country_id']);
        $state = state::where('id', $validated['state_id'])
            ->where('country_id', $validated['country_id'])
            ->first();
        $city = !empty($validated['city_id'])
            ? city::where('id', $validated['city_id'])
                ->where('state_id', $validated['state_id'])
                ->first()
            : null;

        if (!$state || (!empty($validated['city_id']) && !$city)) {
            return back()
                ->withErrors(['city_id' => 'Please select a valid location.'])
                ->withInput();
        }

        $validated['country_code']  = $country->iso2 ?? null;
        $validated['currency']      = $country->currency ?? 'USD';
        $validated['currency_sing'] = $country->currency_symbol ?? '$';
        $validated['country'] = $country->name;
        $validated['state'] = $state->name;
        $validated['city'] = $city?->name;

        unset($validated['country_id'], $validated['state_id'], $validated['city_id']);

        // Reference handling
        if (!empty($request->reference) &&
            UserHasRefs::where('ref', $request->reference)->exists()) {

            $validated['reference'] = $request->reference;

        } else {
            $validated['reference'] = config('app.ref');
        }

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class, RegistrationIdentityGuard::uniqueEmailAliasRule()],
            'phone' => ['required', 'string', 'max:25', RegistrationIdentityGuard::uniquePhoneRule()],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $reference = null;

        if (config('app.comission')) {
            if ($request('reference') && $request('reference') != config('app.ref')) {
                if (UserHasRefs::where('ref', $request('reference'))->exists()) {
                    $reference = $request('reference');
                } else {
                    $reference = config('app.ref');
                    // $isRef = today();
                }
            } else {
                $reference = config('app.ref'); // default reference
            }
        }


        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'reference' => $reference,
        ]);


        /**
         * user has a ref
         */
        // if (config('app.comission')) {

        //     $length = strlen($user->id);

        //     if ($length >= 4) {
        //         $ref = $user->id;
        //     } else {
        //         $ref = str_pad($user->id, 3, '0', STR_PAD_LEFT);
        //     }

        //     UserHasRefs::create([
        //         'ref' => date('ym') . $ref,
        //         'user_id' => $user->id,
        //         'status' => 1,
        //     ]);
        // }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
