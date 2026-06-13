<?php

namespace App\Support;

use App\Models\User;
use Closure;

class RegistrationIdentityGuard
{
    public static function uniquePhoneRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $phone = self::normalizePhone((string) $value);

            if ($phone === '') {
                return;
            }

            $exists = User::query()
                ->whereNotNull('phone')
                ->select(['id', 'phone'])
                ->get()
                ->contains(fn (User $user) => self::normalizePhone((string) $user->phone) === $phone);

            if ($exists) {
                $fail('This phone number already has an account.');
            }
        };
    }

    public static function uniqueEmailAliasRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $email = self::normalizeEmail((string) $value);

            if ($email === '') {
                return;
            }

            $exists = User::query()
                ->whereNotNull('email')
                ->select(['id', 'email'])
                ->get()
                ->contains(fn (User $user) => self::normalizeEmail((string) $user->email) === $email);

            if ($exists) {
                $fail('This email already has an account.');
            }
        };
    }

    public static function normalizeEmail(string $email): string
    {
        $email = strtolower(trim($email));

        if (!str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);

        if (in_array($domain, ['gmail.com', 'googlemail.com'], true)) {
            $local = explode('+', $local, 2)[0];
            $local = str_replace('.', '', $local);
            $domain = 'gmail.com';
        }

        return $local . '@' . $domain;
    }

    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($phone, '880') && strlen($phone) === 13) {
            return '0' . substr($phone, 3);
        }

        if (str_starts_with($phone, '1') && strlen($phone) === 10) {
            return '0' . $phone;
        }

        return $phone;
    }
}
