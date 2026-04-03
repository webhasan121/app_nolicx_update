<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\country;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\user_has_refs;

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',

            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
            'city_id'    => 'nullable|exists:cities,id',

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $country = country::find($request->country_id);

        $validated['country_code']  = $country->iso2 ?? null;
        $validated['currency']      = $country->currency ?? 'USD';
        $validated['currency_sing'] = $country->currency_symbol ?? '$';

        // Reference handling
        if (!empty($request->reference) &&
            user_has_refs::where('ref', $request->reference)->exists()) {

            $validated['reference'] = $request->reference;
            $validated['reference_accepted_at'] = now();

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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $reference = null;

        if (config('app.comission')) {
            $isRef = null;
            if ($request('reference') && $request('reference') != config('app.ref')) {
                if (user_has_refs::where('ref', $request('reference'))->exists()) {
                    $reference = $request('reference');
                    $isRef = today();
                } else {
                    $reference = config('app.ref');
                    // $isRef = today();
                }
            } else {
                $reference = config('app.ref'); // default reference
            }
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'reference' => $reference,
            'reference_accepted_at' => $isRef,
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

        //     user_has_refs::create([
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
