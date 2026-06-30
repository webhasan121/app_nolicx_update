<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\country;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Str;
use App\Support\TranslationManager;
use App\Support\SystemSettings;
use App\Support\CountrySelection;
use Illuminate\Support\Collection;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [


            'auth' => function () use ($request) {
                $user = auth()->user();

                if ($user) {
                    $roles = $user->getRoleNames();

                    $isDashboardRequest = $request->is('dashboard') || $request->is('dashboard/*');

                    $resolvedActiveNav = $this->resolveActiveNav(
                        $roles,
                        $user->active_nav,
                        $isDashboardRequest
                    );

                    if ($resolvedActiveNav !== $user->active_nav) {
                        $user->active_nav = $resolvedActiveNav;
                        $user->save();
                    }
                }

                return [
                    'user' =>  $user
                        ? $user->loadCount(['myCarts', 'myOrderAsUser'])->load('roles')
                        : null,
                    'roles' => $user
                        ? $user->getRoleNames()->values()->all()
                        : [],
                    'cartCount' => $user?->myCarts()->count() ?? 0,
                    'availableCoin' => $user?->abailCoin() ?? 0,
                    'shopSlug' => $user ? Str::slug($user->name) : null,
                ];
            },
            'permissions' => function () {
                $user = auth()->user();

                return $user
                    ? $user->getAllPermissions()->pluck('name')->values()->all()
                    : [];
            },
            'language' => function () {
                TranslationManager::bootstrapDefaults();

                $locale = app()->getLocale();

                return [
                    'current' => $locale,
                    'fixed' => false,
                    'messages' => TranslationManager::messages($locale),
                    'available' => collect(TranslationManager::languages())
                        ->filter(fn ($language) => $language['is_active'])
                        ->map(fn ($language) => [
                            'name' => $language['name'],
                            'code' => $language['code'],
                            'icon' => $language['icon'],
                            'is_default' => $language['is_default'],
                        ])
                        ->values()
                        ->all(),
                ];
            },
            'selectedCountry' => fn() => CountrySelection::fromRequest($request),

            // Only for frontend routes
            'global' => function () {
                if (!request()->routeIs('home', 'shops.*', 'category.*', 'products.*', 'product.*', 'web.pages', 'search')) return [];

                return [
                    'categories' => \App\Models\Category::getAll(),
                    'navigations' => \App\Models\Navigations::with('links')->get(),
                    'countries' => cache()->remember(
                        'frontend_countries',
                        3600,
                        fn() => country::select('id', 'name')->orderBy('name')->get()
                    ),
                    'branches' => fn() => cache()->remember(
                        'branches',
                        3600,
                        fn() => Branch::select('id', 'name', 'address', 'phone', 'email')->get()
                    ),
                ];
            },
            'appConfig' => [
                'playstore_link' => config('app.playstore_link'),
                'dbid_no' => config('app.dbid_no'),
                'trade_license' => config('app.trade_license'),
                'whatsapp_no' => config('app.whatsapp_no'),
                'support_mail' => config('app.support_mail'),
                'currency' => SystemSettings::defaultCurrency(),
            ],
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'warning' => $request->session()->get('warning'),
                    'info' => $request->session()->get('info'),
                    'message' => $request->session()->get('message'),
                    'error' => $request->session()->get('error'),
                ];
            },
        ]);
    }

    private function resolveActiveNav(Collection $roles, ?string $current, bool $isDashboardRoute = false): string
    {
        $roles = $roles->filter()->values();

        $dashboardRoles = collect(['system', 'vendor', 'reseller', 'rider']);
        $hasDashboardRole = $roles->intersect($dashboardRoles)->isNotEmpty();

        if (
            $current &&
            $roles->contains($current) &&
            !($isDashboardRoute && $current === 'user' && $hasDashboardRole)
        ) {
            return $current;
        }

        if ($roles->contains('system') || $roles->contains('admin')) {
            return 'system';
        }

        foreach (['vendor', 'reseller', 'rider'] as $dashboardRole) {
            if ($roles->contains($dashboardRole)) {
                return $dashboardRole;
            }
        }

        return $roles->first() ?? 'user';
    }
}
