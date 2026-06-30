<?php

namespace App\Support;

use App\Models\FcmDeviceToken;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmPushNotification
{
    public function sendNotice(Notice $notice, string $action = 'created'): void
    {
        if (! $this->shouldSend($notice, $action)) {
            return;
        }

        $tokens = $this->tokensForNotice($notice);

        if ($tokens->isEmpty()) {
            return;
        }

        $payload = NoticeRealtimePayload::serialize($notice->loadMissing('creator:id,name', 'order'));

        foreach ($tokens->chunk(400) as $chunk) {
            foreach ($chunk as $token) {
                $this->sendToToken($token, $notice, $payload, $action);
            }
        }
    }

    private function shouldSend(Notice $notice, string $action): bool
    {
        if (! in_array($action, ['created', 'updated'], true)) {
            return false;
        }

        if (! $notice->is_active) {
            return false;
        }

        if ($notice->published_at && $notice->published_at->isFuture()) {
            return false;
        }

        if ($notice->expires_at && $notice->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    private function tokensForNotice(Notice $notice)
    {
        $query = FcmDeviceToken::query()
            ->where('platform', 'android')
            ->whereNotNull('token');

        if ($this->isRoleWideNotice($notice)) {
            $roles = NoticeRealtimePayload::rolesFor($notice);
            $query->whereHas('user', function ($userQuery) use ($roles) {
                $userQuery->where(function ($roleQuery) use ($roles) {
                    foreach ($roles as $role) {
                        if ($role === 'system') {
                            $roleQuery->orWhereHas('roles', fn ($query) => $query->whereIn('name', ['system', 'admin']));
                        } else {
                            $roleQuery->orWhereHas('roles', fn ($query) => $query->where('name', $role));
                        }
                    }
                });
            });
        } else {
            $userIds = $this->recipientUserIds($notice);

            if (! count($userIds)) {
                return collect();
            }

            $query->whereIn('user_id', $userIds);
        }

        return $query
            ->get(['id', 'token'])
            ->unique('token');
    }

    private function isRoleWideNotice(Notice $notice): bool
    {
        return ! $notice->target_user_id && ! $notice->order_id;
    }

    private function recipientUserIds(Notice $notice): array
    {
        if ($notice->target_user_id && ! $notice->order_id) {
            return [(int) $notice->target_user_id];
        }

        $payload = NoticeRealtimePayload::serialize($notice->loadMissing('order'));
        $roles = NoticeRealtimePayload::rolesFor($notice);
        $userIds = collect();

        if ($notice->target_user_id) {
            $userIds->push((int) $notice->target_user_id);
        }

        if (in_array('user', $roles, true) && $notice->order?->user_id) {
            $userIds->push((int) $notice->order->user_id);
        }

        if (in_array('system', $roles, true)) {
            $userIds = $userIds->merge(
                User::query()
                    ->whereHas('roles', fn ($query) => $query->whereIn('name', ['system', 'admin']))
                    ->pluck('id')
            );
        }

        if (in_array('vendor', $roles, true)) {
            $userIds = $userIds->merge($payload['vendor_ids'] ?? []);
        }

        if (
            in_array('reseller', $roles, true)
            && ($payload['seller_type'] ?? null) === 'reseller'
            && ! empty($payload['seller_id'])
        ) {
            $userIds->push((int) $payload['seller_id']);
        }

        if (in_array('rider', $roles, true)) {
            $userIds = $userIds->merge($payload['rider_ids'] ?? []);
        }

        return $userIds
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function sendToToken(FcmDeviceToken $deviceToken, Notice $notice, array $payload, string $action): void
    {
        try {
            if ($this->canUseV1()) {
                $this->sendV1($deviceToken, $notice, $payload, $action);
                return;
            }

            if (config('services.fcm.server_key')) {
                $this->sendLegacy($deviceToken, $notice, $payload, $action);
            }
        } catch (\Throwable $exception) {
            Log::warning('FCM push notification failed', [
                'notice_id' => $notice->id,
                'token_id' => $deviceToken->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function sendV1(FcmDeviceToken $deviceToken, Notice $notice, array $payload, string $action): void
    {
        $projectId = $this->projectId();

        if (! $projectId) {
            return;
        }

        $response = Http::withToken($this->accessToken())
            ->timeout(10)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'message' => [
                    'token' => $deviceToken->token,
                    'notification' => [
                        'title' => $notice->title,
                        'body' => $notice->body,
                    ],
                    'data' => $this->dataPayload($payload, $action),
                    'android' => [
                        'priority' => 'HIGH',
                        'notification' => [
                            'channel_id' => config('services.fcm.android_channel_id', 'default'),
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                ],
            ]);

        $this->handleResponse($response->status(), $response->json(), $deviceToken);
    }

    private function sendLegacy(FcmDeviceToken $deviceToken, Notice $notice, array $payload, string $action): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'key=' . config('services.fcm.server_key'),
        ])->timeout(10)->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $deviceToken->token,
            'priority' => 'high',
            'notification' => [
                'title' => $notice->title,
                'body' => $notice->body,
                'sound' => 'default',
            ],
            'data' => $this->dataPayload($payload, $action),
        ]);

        $this->handleResponse($response->status(), $response->json(), $deviceToken);
    }

    private function dataPayload(array $payload, string $action): array
    {
        return collect([
            'type' => 'notice',
            'action' => $action,
            'notice_id' => $payload['id'] ?? null,
            'order_id' => $payload['order_id'] ?? null,
            'target_user_id' => $payload['target_user_id'] ?? null,
            'seller_id' => $payload['seller_id'] ?? null,
            'seller_type' => $payload['seller_type'] ?? null,
            'link_urls' => json_encode($payload['link_urls'] ?? []),
            'target_roles' => json_encode($payload['target_roles'] ?? []),
        ])->map(fn ($value) => $value === null ? '' : (string) $value)->all();
    }

    private function handleResponse(int $status, ?array $body, FcmDeviceToken $deviceToken): void
    {
        if ($status >= 200 && $status < 300) {
            $deviceToken->forceFill(['last_used_at' => now()])->save();
            return;
        }

        $error = data_get($body, 'error.status')
            ?? data_get($body, 'results.0.error')
            ?? data_get($body, 'error.message');

        if (in_array($error, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NotRegistered', 'InvalidRegistration'], true)) {
            $deviceToken->delete();
        }

        Log::warning('FCM returned an error', [
            'status' => $status,
            'error' => $error,
            'token_id' => $deviceToken->id,
        ]);
    }

    private function canUseV1(): bool
    {
        return (bool) ($this->projectId() && $this->serviceAccount());
    }

    private function projectId(): ?string
    {
        return config('services.fcm.project_id') ?: data_get($this->serviceAccount(), 'project_id');
    }

    private function accessToken(): string
    {
        return Cache::remember('fcm_access_token', 3300, function () {
            $account = $this->serviceAccount();
            $now = time();
            $jwt = $this->jwt([
                'iss' => $account['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $account['token_uri'] ?? 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ], $account['private_key']);

            $response = Http::asForm()->timeout(10)->post($account['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Unable to get FCM access token: ' . $response->body());
            }

            return (string) $response->json('access_token');
        });
    }

    private function jwt(array $payload, string $privateKey): string
    {
        $segments = [
            $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])),
            $this->base64UrlEncode(json_encode($payload)),
        ];

        openssl_sign(implode('.', $segments), $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function serviceAccount(): ?array
    {
        $json = config('services.fcm.service_account_json');
        $path = config('services.fcm.service_account_path');
        $account = null;

        if ($json) {
            $account = json_decode($json, true);
        }

        if (! $account && $path && is_file($path)) {
            $account = json_decode(file_get_contents($path), true);
        }

        if (! is_array($account)) {
            return null;
        }

        if (! empty($account['private_key'])) {
            $account['private_key'] = str_replace('\\n', "\n", $account['private_key']);
        }

        return $account;
    }
}
