<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Support\LanguageCatalog;
use App\Support\TranslationManager;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        TranslationManager::bootstrapDefaults();

        $search = trim((string) $request->query('search', ''));
        $total = TranslationManager::totalKeys();

        $languages = collect(TranslationManager::languages())
            ->filter(function ($language) use ($search) {
                if ($search === '') {
                    return true;
                }

                return str_contains(strtolower($language['name']), strtolower($search))
                    || str_contains(strtolower($language['code']), strtolower($search))
                    || str_contains(strtolower($language['icon']), strtolower($search));
            })
            ->map(function ($language) use ($total) {
                $done = collect(TranslationManager::messages($language['code']))
                    ->filter(fn ($value) => trim((string) $value) !== '')
                    ->count();

                return [
                    'id' => $language['code'],
                    'name' => $language['name'],
                    'code' => strtoupper($language['code']),
                    'locale' => $language['code'],
                    'icon' => $language['icon'],
                    'done' => $done,
                    'total' => $total,
                    'progress' => $total > 0 ? round(($done / $total) * 100) : 0,
                    'is_default' => $language['is_default'],
                    'is_active' => $language['is_active'],
                    'edit_url' => route('system.languages.edit', $language['code']),
                ];
            })
            ->sortBy('name')
            ->values();

        return Inertia::render('Auth/system/languages/Index', [
            'pageTitle' => 'Translations',
            'languages' => $languages,
            'summary' => [
                'count' => count(TranslationManager::languages()),
                'total' => $total,
            ],
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function edit(string $locale)
    {
        TranslationManager::bootstrapDefaults();

        $language = TranslationManager::findLanguage($locale);

        abort_if(! $language, 404);

        $defaultLanguage = TranslationManager::findLanguage(config('app.locale', 'en'))
            ?: TranslationManager::languages()[0];
        $source = array_replace(TranslationManager::defaultMessages(), TranslationManager::messages($defaultLanguage['code']));
        $target = TranslationManager::messages($language['code']);

        return Inertia::render('Auth/system/languages/Edit', [
            'pageTitle' => "Translations | {$language['name']}",
            'languageItem' => [
                'id' => $language['code'],
                'name' => $language['name'],
                'code' => $language['code'],
                'icon' => $language['icon'],
            ],
            'defaultLanguage' => [
                'name' => $defaultLanguage['name'],
                'code' => $defaultLanguage['code'],
            ],
            'translations' => collect($source)
                ->map(fn ($value, $key) => [
                    'key' => $key,
                    'source' => $value,
                    'value' => $target[$key] ?? '',
                ])
                ->values(),
        ]);
    }

    public function update(Request $request, string $locale)
    {
        $language = TranslationManager::findLanguage($locale);

        abort_if(! $language, 404);

        $validated = $request->validate([
            'translations' => ['required', 'array'],
            'translations.*.key' => ['required', 'string', 'max:191'],
            'translations.*.value' => ['nullable', 'string'],
        ]);

        $messages = collect($validated['translations'])
            ->mapWithKeys(fn ($item) => [$item['key'] => $item['value'] ?? ''])
            ->all();

        TranslationManager::writeMessages($language['code'], $messages);

        return redirect()
            ->route('system.languages.edit', $language['code'])
            ->with('success', 'Translations saved successfully.');
    }

    public function setDefault(string $locale)
    {
        $language = TranslationManager::findLanguage($locale);

        abort_if(! $language, 404);

        session(['locale' => $language['code']]);

        return back()->with('success', "{$language['name']} is now selected.");
    }

    public function toggle(string $locale)
    {
        $language = TranslationManager::findLanguage($locale);

        abort_if(! $language, 404);

        if (! $language['is_active']) {
            TranslationManager::writeMessages(
                $language['code'],
                array_replace(TranslationManager::defaultMessages(), TranslationManager::messages($language['code']))
            );
        }

        return back()->with('success', 'Language file is ready.');
    }
}
