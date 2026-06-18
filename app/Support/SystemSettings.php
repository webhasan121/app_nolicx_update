<?php

namespace App\Support;

class SystemSettings
{
    public const DEFAULT_CURRENCY_CODE = 'BDT';
    public const DEFAULT_CURRENCY_NAME = 'Bangladeshi Taka';
    public const DEFAULT_CURRENCY_SYMBOL = 'TK';

    private const BUILTIN_CURRENCIES = [
        ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => '$'],
        ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => 'TK'],
        ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$'],
        ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => '$'],
        ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'Fr'],
        ['code' => 'CLP', 'name' => 'Chilean Peso', 'symbol' => '$'],
        ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥'],
        ['code' => 'CZK', 'name' => 'Czech Koruna', 'symbol' => 'Kč'],
        ['code' => 'DKK', 'name' => 'Danish Krone', 'symbol' => 'kr'],
        ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
        ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
        ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => '$'],
        ['code' => 'HUF', 'name' => 'Hungarian Forint', 'symbol' => 'Ft'],
        ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp'],
        ['code' => 'ILS', 'name' => 'Israeli New Shekel', 'symbol' => '₪'],
        ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹'],
        ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥'],
        ['code' => 'KRW', 'name' => 'Korean Won', 'symbol' => '₩'],
        ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$'],
        ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM'],
        ['code' => 'NOK', 'name' => 'Norwegian Krone', 'symbol' => 'kr'],
        ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => '$'],
        ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => '₱'],
        ['code' => 'PKR', 'name' => 'Pakistan Rupee', 'symbol' => '₨'],
        ['code' => 'PLN', 'name' => 'Polish Zloty', 'symbol' => 'zł'],
        ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽'],
        ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr'],
        ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => '$'],
        ['code' => 'THB', 'name' => 'Thai Baht', 'symbol' => '฿'],
        ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺'],
        ['code' => 'TWD', 'name' => 'Taiwan Dollar', 'symbol' => '$'],
        ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
        ['code' => 'VEF', 'name' => 'Bolivar Fuerte', 'symbol' => 'Bs.'],
        ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R'],
    ];

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

    public static function currencies(): array
    {
        $decoded = json_decode(self::get('CURRENCIES', '[]'), true);
        $currencies = is_array($decoded) && count($decoded) > 0 ? $decoded : self::BUILTIN_CURRENCIES;
        $currencies = collect($currencies)
            ->map(fn ($item) => [
                'code' => strtoupper(trim((string) ($item['code'] ?? ''))),
                'name' => trim((string) ($item['name'] ?? '')),
                'symbol' => trim((string) ($item['symbol'] ?? '')),
            ])
            ->filter(fn ($item) => $item['code'] !== '' && $item['name'] !== '' && $item['symbol'] !== '')
            ->unique('code')
            ->values()
            ->all();

        return $currencies;
    }

    public static function defaultCurrency(): array
    {
        $defaultCode = strtoupper(self::get('DEFAULT_CURRENCY', self::DEFAULT_CURRENCY_CODE));
        $currency = collect(self::currencies())->firstWhere('code', $defaultCode);

        return $currency ?: [
            'code' => self::DEFAULT_CURRENCY_CODE,
            'name' => self::DEFAULT_CURRENCY_NAME,
            'symbol' => self::DEFAULT_CURRENCY_SYMBOL,
        ];
    }

    public static function saveCurrencies(array $currencies): void
    {
        self::set('CURRENCIES', json_encode($currencies, JSON_UNESCAPED_SLASHES));
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
