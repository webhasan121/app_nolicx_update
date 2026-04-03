<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiResponse;
use App\HandleImageUpload;
use Illuminate\Validation\Rules;
use App\Models\user_has_refs;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth as AuthP;
use Illuminate\Support\Facades\DB;

class Auth extends Controller
{
    use HandleImageUpload;
    private $userId;
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'country' => ['required', 'string', 'max:25'],
            'gender' => ['nullable', 'max:10'],
            'phone' => ['required'],
            'profile_photo' => 'required',
            'password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
        ]);

        $reference = null;
        $isRef = null;


        // active reference 
        if (config('app.comission')) {
            if ($request->reference && $request->reference != config('app.ref')) {
                if (user_has_refs::where('ref', $request->reference)->exists()) {
                    $reference = $request->reference;
                    $isRef = today();
                } else {
                    $reference = config('app.ref');
                    // $isRef = today();
                }
            } else {
                $reference = config('app.ref'); // default reference
            }
        }

        try {

            DB::transaction(function () use ($request, $reference, $isRef) {

                $this->userId = User::insertGetId([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'country' => $request->country,
                    'gender' => $request->gender,
                    'reference' => $reference,
                    'profile_photo_path' => $this->handleImageUpload($request->profile_photo, 'profile', null),
                    'reference_accepted_at' => $isRef,
                ]);
            });
            if ($this->userId) {
                /**
                 * user has a ref code
                 */
                if (config('app.comission')) {

                    $length = strlen($this->userId);

                    if ($length >= 4) {
                        $ref = $this->userId;
                    } else {
                        $ref = str_pad($this->userId, 3, '0', STR_PAD_LEFT);
                    }


                    user_has_refs::create([
                        'ref' => date('ym') . $ref,
                        'user_id' => $this->userId,
                        'status' => 1,
                    ]);
                }
                try {
                    //code...
                    AuthP::attempt($request->Only(['email', 'password']));
                    $token = $request->user()->createToken(AuthP::getName());

                    // return ['token' => $token->plainTextToken];
                    return ApiResponse::success(['token' => $token->plainTextToken]);
                } catch (\Throwable $th) {
                    return ApiResponse::unauthorized($th->getMessage());
                }
            }
        } catch (\Throwable $th) {
            return ApiResponse::error('Error While Register', $th->getMessage(), 422);
        }
    }
}
