<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\FcmDeviceToken;
use Illuminate\Http\Request;

class FcmDeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:4096'],
            'platform' => ['nullable', 'string', 'max:40'],
            'device_id' => ['nullable', 'string', 'max:255'],
            'app_version' => ['nullable', 'string', 'max:100'],
        ]);

        $token = trim($validated['token']);
        $hash = FcmDeviceToken::hashToken($token);

        $deviceToken = FcmDeviceToken::query()->updateOrCreate(
            ['token_hash' => $hash],
            [
                'user_id' => $request->user()->id,
                'token' => $token,
                'platform' => $validated['platform'] ?? 'android',
                'device_id' => $validated['device_id'] ?? null,
                'app_version' => $validated['app_version'] ?? null,
                'last_used_at' => now(),
            ]
        );

        return ApiResponse::success([
            'id' => $deviceToken->id,
            'platform' => $deviceToken->platform,
            'device_id' => $deviceToken->device_id,
        ], 'FCM token registered');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'token' => ['nullable', 'string', 'max:4096'],
            'device_id' => ['nullable', 'string', 'max:255'],
        ]);

        $query = FcmDeviceToken::query()->where('user_id', $request->user()->id);

        if (! empty($validated['token'])) {
            $query->where('token_hash', FcmDeviceToken::hashToken(trim($validated['token'])));
        } elseif (! empty($validated['device_id'])) {
            $query->where('device_id', $validated['device_id']);
        } else {
            return ApiResponse::error('token or device_id is required', null, 422);
        }

        $query->delete();

        return ApiResponse::success(null, 'FCM token removed');
    }
}
