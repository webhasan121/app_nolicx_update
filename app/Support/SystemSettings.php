<?php

namespace App\Support;

class SystemSettings
{
    public static function get(string $key, string $default = ''): string
    {
        $settings = self::all();

        if (array_key_exists($key, $settings)) {
            return (string) $settings[$key];
        }

        return (string) env($key, $default);
    }

    public static function set(string $key, string $value): void
    {
        $settings = self::all();
        $settings[$key] = $value;

        self::write($settings);
    }

    private static function all(): array
    {
        $path = self::path();

        if (!file_exists($path)) {
            return [];
        }

        $contents = file_get_contents($path);
        $settings = json_decode($contents ?: '{}', true);

        return is_array($settings) ? $settings : [];
    }

    private static function write(array $settings): void
    {
        $path = self::path();
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents(
            $path,
            json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            LOCK_EX
        );
    }

    private static function path(): string
    {
        return storage_path('app/system-settings.json');
    }
}
