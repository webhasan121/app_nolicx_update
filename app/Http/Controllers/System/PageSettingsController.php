<?php

namespace App\Http\Controllers\System;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\page_settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PageSettingsController extends Controller
{
    use HandleImageUpload;

    public function indexReact(Request $request): Response
    {
        $find = trim((string) $request->query('find', ''));
        $pages = page_settings::query()
            ->when($find !== '', function ($query) use ($find) {
                $query->where(function ($subQuery) use ($find) {
                    $subQuery
                        ->where('id', 'like', '%' . $find . '%')
                        ->orWhere('name', 'like', '%' . $find . '%')
                        ->orWhere('slug', 'like', '%' . $find . '%')
                        ->orWhere('title', 'like', '%' . $find . '%')
                        ->orWhere('content', 'like', '%' . $find . '%')
                        ->orWhere('status', 'like', '%' . $find . '%');
                });
            })
            ->latest('id')
            ->paginate(config('app.paginate'))
            ->withQueryString();

        return Inertia::render('Auth/system/settings/pages/index', [
            'filters' => [
                'find' => $find,
            ],
            'pages' => [
                'data' => $pages->getCollection()->map(fn (page_settings $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'title' => $item->title,
                    'content' => Str::limit($item->content, 100, '...'),
                    'status' => $item->status,
                ])->values()->all(),
                'links' => collect($pages->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $pages->firstItem(),
                'to' => $pages->lastItem(),
                'total' => $pages->total(),
            ],
            'printUrl' => route('system.pages.print', [
                'find' => $find,
            ]),
        ]);
    }

    public function printReact(Request $request): Response
    {
        $find = trim((string) $request->query('find', ''));
        $pages = page_settings::query()
            ->when($find !== '', function ($query) use ($find) {
                $query->where(function ($subQuery) use ($find) {
                    $subQuery
                        ->where('id', 'like', '%' . $find . '%')
                        ->orWhere('name', 'like', '%' . $find . '%')
                        ->orWhere('slug', 'like', '%' . $find . '%')
                        ->orWhere('title', 'like', '%' . $find . '%')
                        ->orWhere('content', 'like', '%' . $find . '%')
                        ->orWhere('status', 'like', '%' . $find . '%');
                });
            })
            ->latest('id')
            ->get();

        return Inertia::render('Auth/system/settings/pages/Print', [
            'filters' => [
                'find' => $find,
            ],
            'pages' => $pages->map(fn (page_settings $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'title' => $item->title,
                'content' => Str::limit($item->content, 100, '...'),
                'status' => $item->status,
            ])->values()->all(),
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($page = page_settings::findOrFail($id)) {
            $page->delete();
        }

        return redirect()->back()->with('success', 'Page Deleted !');
    }

    public function createReact(Request $request): Response
    {
        $pageSlug = $request->query('page');
        $data = null;

        if ($pageSlug) {
            $data = page_settings::where('slug', $pageSlug)->first();
        }

        return Inertia::render('Auth/system/settings/pages/Create', [
            'pages' => page_settings::all()->map(fn (page_settings $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
            ])->values()->all(),
            'pageQuery' => $pageSlug,
            'pageData' => $data ? [
                'id' => $data->id,
                'name' => $data->name,
                'slug' => $data->slug,
                'title' => $data->title,
                'keyword' => $data->keyword,
                'description' => $data->description,
                'content' => $data->content,
                'thumbnail' => $data->thumbnail,
            ] : null,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $pageId = $request->input('id');

        $validated = $request->validate([
            'name' => ['required'],
            'slug' => ['required', 'unique:page_settings,slug,' . $pageId],
            'title' => ['nullable'],
            'keyword' => ['nullable'],
            'description' => ['nullable'],
            'content' => ['nullable'],
            'thumbnail' => ['nullable'],
            'page' => ['nullable'],
            'id' => ['nullable'],
        ]);

        $existing = null;
        if (!empty($validated['page'])) {
            $existing = page_settings::where('slug', $validated['page'])->first();
        }

        if (!$existing && !empty($validated['id'])) {
            $existing = page_settings::find($validated['id']);
        }

        $thumbnail = $this->handleImageUpload(
            $request->file('thumbnail'),
            'pages',
            $existing?->thumbnail
        );

        if ($existing) {
            $existing->update([
                'slug' => $validated['slug'],
                'name' => $validated['name'],
                'title' => $validated['title'] ?? null,
                'keyword' => $validated['keyword'] ?? null,
                'description' => $validated['description'] ?? null,
                'content' => $validated['content'] ?? null,
                'thumbnail' => $thumbnail,
            ]);

            return redirect()->route('system.pages.create', [
                'page' => $existing->fresh()->slug,
            ])->with('success', 'Saved !');
        }

        $page = page_settings::create([
            'slug' => $validated['slug'],
            'name' => $validated['name'],
            'title' => $validated['title'] ?? null,
            'keyword' => $validated['keyword'] ?? null,
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'thumbnail' => $thumbnail,
        ]);

        return redirect()->route('system.pages.create', [
            'page' => $page->slug,
        ])->with('success', 'Saved !');
    }
}
