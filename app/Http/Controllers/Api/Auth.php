<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiResponse;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use App\Models\UserHasRefs;
use App\Models\User;
use App\Support\RegistrationIdentityGuard;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class Auth extends Controller
{
    public function register(Request $request)
    {
        $this->mergeConfirmedPassword($request);
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class, RegistrationIdentityGuard::uniqueEmailAliasRule()],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:25', RegistrationIdentityGuard::uniquePhoneRule()],
            'reference' => ['nullable', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => [
                'required',
                'integer',
                Rule::exists('states', 'id')->where('country_id', $request->integer('country_id')),
            ],
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where('state_id', $request->integer('state_id')),
            ],
        ]);

        try {
            $country = country::findOrFail($validated['country_id']);
            $state = state::findOrFail($validated['state_id']);
            $city = !empty($validated['city_id']) ? city::find($validated['city_id']) : null;
            $reference = config('app.ref');

            if (!empty($validated['reference']) && $validated['reference'] !== config('app.ref')) {
                if (UserHasRefs::where('ref', $validated['reference'])->exists()) {
                    $reference = $validated['reference'];
                }
            }

            $user = DB::transaction(function () use ($validated, $country, $state, $city, $reference) {
                return User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'phone' => $validated['phone'],
                    'country' => $country->name,
                    'country_code' => $country->iso2,
                    'state' => $state->name,
                    'city' => $city?->name,
                    'reference' => $reference,
                ]);
            });

            event(new Registered($user));

            $token = $user->createToken('postman-api')->plainTextToken;

            return ApiResponse::success([
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->fresh(),
            ], 'Registration successful', 201);
        } catch (\Throwable $th) {
            return ApiResponse::error('Error while register', $th->getMessage(), 422);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return ApiResponse::error('The provided credentials do not match our records.', null, 401);
        }

        $token = $user->createToken('postman-api')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 'Login successful');
    }

    public function me(Request $request)
    {
        return ApiResponse::success($request->user(), 'Authenticated user');
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return ApiResponse::success(null, 'Logout successful');
    }

    public function sendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email already verified');
        }

        $request->user()->sendEmailVerificationNotification();

        return ApiResponse::success(null, 'Verification link sent');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'hash' => ['required', 'string'],
        ]);

        $user = $request->user();

        if ((int) $validated['id'] !== (int) $user->getKey()) {
            return ApiResponse::error('Invalid verification user.', null, 403);
        }

        if (!hash_equals((string) $validated['hash'], sha1($user->getEmailForVerification()))) {
            return ApiResponse::error('Invalid verification hash.', null, 403);
        }

        if (!$user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return ApiResponse::success($user->fresh(), 'Email verified successfully');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return ApiResponse::error(__($status), null, 422);
        }

        return ApiResponse::success(null, __($status));
    }

    public function resetPassword(Request $request)
    {
        $this->mergeConfirmedPassword($request);

        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return ApiResponse::error(__($status), null, 422);
        }

        return ApiResponse::success(null, __($status));
    }

    private function mergeConfirmedPassword(Request $request): void
    {
        if (!$request->has('password_confirmation') && $request->has('confirmed')) {
            $request->merge([
                'password_confirmation' => $request->input('confirmed'),
            ]);
        }
    }
}
