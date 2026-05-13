<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class TranslationManager
{
    public const GROUP = 'admin';

    public static function bootstrapDefaults(): void
    {
        foreach (self::languages() as $language) {
            $messages = self::messages($language['code']);

            $defaults = self::defaultMessages();

            if (! $messages || count(array_diff_key($defaults, $messages)) > 0) {
                self::writeMessages($language['code'], array_replace($defaults, $messages));
            }
        }
    }

    public static function currentLocale(): string
    {
        $candidate = session('locale') ?: auth()->user()?->site_language ?: auth()->user()?->language;

        if ($candidate && self::findLanguage($candidate)) {
            return $candidate;
        }

        return config('app.locale', 'en');
    }

    public static function messages(string $locale): array
    {
        $file = self::filePath($locale);

        if (!File::exists($file)) {
            return [];
        }

        $messages = require $file;

        return is_array($messages) ? $messages : [];
    }

    public static function writeMessages(string $locale, array $messages): void
    {
        File::ensureDirectoryExists(dirname(self::filePath($locale)));

        ksort($messages);

        File::put(
            self::filePath($locale),
            "<?php\n\nreturn " . var_export($messages, true) . ";\n"
        );
    }

    public static function languages(): array
    {
        return collect(LanguageCatalog::languages())
            ->map(fn ($language, $index) => [
                ...$language,
                'id' => $language['code'],
                'locale' => $language['code'],
                'is_default' => $language['code'] === config('app.locale', 'en'),
                'is_active' => File::exists(self::filePath($language['code'])),
                'sort_order' => $index + 1,
            ])
            ->all();
    }

    public static function findLanguage(string $locale): ?array
    {
        return collect(self::languages())->firstWhere('code', $locale);
    }

    public static function filePath(string $locale): string
    {
        return base_path("lang/{$locale}/" . self::GROUP . '.php');
    }

    public static function totalKeys(): int
    {
        return count(self::defaultMessages());
    }

    public static function defaultMessages(): array
    {
        return array_replace(LanguageCatalog::keys(), self::categoryMessages());
    }

    private static function categoryMessages(): array
    {
        if (! Schema::hasTable('categories')) {
            return [];
        }

        return Category::query()
            ->whereNotNull('name')
            ->pluck('name')
            ->filter()
            ->unique()
            ->mapWithKeys(fn ($name) => [(string) $name => (string) $name])
            ->all();
    }
}
