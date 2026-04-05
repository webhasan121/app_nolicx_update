<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Navigations;
use App\Models\Navigations_has_link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NavigationController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $menus = Navigations::with('links')->get();
        $selectedMenuId = $request->integer('menu');
        $selectedMenu = $selectedMenuId ? Navigations::with('links')->find($selectedMenuId) : null;
        $fresh = $request->boolean('fresh');

        return Inertia::render('Auth/system/navigations/index', [
            'menus' => $menus->map(fn (Navigations $menu) => $this->serializeMenu($menu))->values()->all(),
            'selectedMenu' => $selectedMenu ? $this->serializeMenu($selectedMenu) : null,
            'selectedMenuItems' => $selectedMenu
                ? ($fresh
                    ? [['name' => 'Give Item name', 'url' => 'Define Item Url', 'navigations_id' => $selectedMenu->id]]
                    : $selectedMenu->links->map(fn (Navigations_has_link $item) => [
                        'id' => $item->id,
                        'name' => $item->name,
                        'url' => $item->url,
                        'navigations_id' => $item->navigations_id,
                    ])->values()->all())
                : [],
        ]);
    }

    public function storeMenu(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        if (!Navigations::where(['name' => $validated['name']])->exists()) {
            $menu = Navigations::create([
                'name' => $validated['name'],
            ]);

            return redirect()->route('system.navigations.index', [
                'menu' => $menu->id,
                'fresh' => 1,
            ]);
        }

        return redirect()->route('system.navigations.index');
    }

    public function renameMenu(Request $request, Navigations $menu): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable',
        ]);

        $menu->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('system.navigations.index', [
            'menu' => $menu->id,
        ]);
    }

    public function destroyMenu(Navigations $menu): RedirectResponse
    {
        $menuId = $menu->id;
        $menu->delete();
        Navigations_has_link::where(['navigations_id' => $menuId])->delete();

        return redirect()->route('system.navigations.index');
    }

    public function updateMenuItems(Request $request, Navigations $menu): RedirectResponse
    {
        $items = collect($request->input('items', []))
            ->map(fn ($item) => [
                'name' => $item['name'] ?? '',
                'url' => $item['url'] ?? '',
                'navigations_id' => $menu->id,
            ])
            ->values()
            ->all();

        Navigations_has_link::where(['navigations_id' => $menu->id])->delete();
        foreach ($items as $item) {
            Navigations_has_link::create($item);
        }

        return redirect()->route('system.navigations.index', [
            'menu' => $menu->id,
        ]);
    }

    public function destroyMenuItem(Navigations_has_link $item): RedirectResponse
    {
        $menuId = $item->navigations_id;
        $item->delete();

        return redirect()->route('system.navigations.index', [
            'menu' => $menuId,
        ]);
    }

    private function serializeMenu(Navigations $menu): array
    {
        return [
            'id' => $menu->id,
            'name' => $menu->name,
            'links' => $menu->links->map(fn (Navigations_has_link $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'url' => $item->url,
                'navigations_id' => $item->navigations_id,
            ])->values()->all(),
        ];
    }
}
