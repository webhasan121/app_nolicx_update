<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Str;

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


            'auth' => function () {
                $user = auth()->user();

                if ($user) {
                    $roles = $user->getRoleNames();

                    if (!$roles->contains($user->active_nav)) {
                        $user->active_nav = $roles[0] ?? $user->active_nav;
                        $user->save();
                    }

                    if (empty($user->active_nav)) {
                        if (count($roles) > 2) {
                            $user->active_nav = $roles[0] ?? $user->active_nav;
                            $user->save();
                        } else {
                            if ($roles->contains('venodor')) {
                                $user->active_nav = 'vendor';
                                $user->save();
                            }

                            if ($roles->contains('reseller')) {
                                $user->active_nav = 'reseller';
                                $user->save();
                            }

                            if ($roles->contains('rider')) {
                                $user->active_nav = 'rider';
                                $user->save();
                            }
                        }
                    }
                }

                return [
                    'user' =>  $user
                        ? $user->loadCount('myCarts')->load('roles')
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

            // Only for frontend routes
            'global' => function () {
                if (!request()->routeIs('home')) return [];

                return [
                    // 'categories' => \App\Models\Category::getAll(),
                    'navigations' => \App\Models\Navigations::with('links')->get(),
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
}
