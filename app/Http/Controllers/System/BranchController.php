<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $find = trim((string) $request->query('find', ''));
        $branches = Branch::query()
            ->when($find !== '', function ($query) use ($find) {
                $query->where(function ($subQuery) use ($find) {
                    $subQuery
                        ->where('id', 'like', '%' . $find . '%')
                        ->orWhere('name', 'like', '%' . $find . '%')
                        ->orWhere('email', 'like', '%' . $find . '%')
                        ->orWhere('phone', 'like', '%' . $find . '%')
                        ->orWhere('slug', 'like', '%' . $find . '%')
                        ->orWhere('type', 'like', '%' . $find . '%');
                });
            })
            ->latest('id')
            ->paginate(config('app.paginate'))
            ->withQueryString();

        return Inertia::render('Auth/system/settings/branch/index', [
            'filters' => [
                'find' => $find,
            ],
            'branches' => [
                'data' => $branches->getCollection()->values()->map(function (Branch $branch, int $index) use ($branches) {
                    return [
                        'sl' => (($branches->currentPage() - 1) * $branches->perPage()) + $index + 1,
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'email' => $branch->email,
                        'type' => $branch->type,
                        'created_at' => (string) $branch->created_at,
                    ];
                })->all(),
                'links' => collect($branches->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $branches->firstItem(),
                'to' => $branches->lastItem(),
                'total' => $branches->total(),
            ],
            'printUrl' => route('system.branches.print', [
                'find' => $find,
            ]),
        ]);
    }

    public function printReact(Request $request): Response
    {
        $find = trim((string) $request->query('find', ''));
        $branches = Branch::query()
            ->when($find !== '', function ($query) use ($find) {
                $query->where(function ($subQuery) use ($find) {
                    $subQuery
                        ->where('id', 'like', '%' . $find . '%')
                        ->orWhere('name', 'like', '%' . $find . '%')
                        ->orWhere('email', 'like', '%' . $find . '%')
                        ->orWhere('phone', 'like', '%' . $find . '%')
                        ->orWhere('slug', 'like', '%' . $find . '%')
                        ->orWhere('type', 'like', '%' . $find . '%');
                });
            })
            ->latest('id')
            ->get();

        return Inertia::render('Auth/system/settings/branch/Print', [
            'filters' => [
                'find' => $find,
            ],
            'branches' => $branches->values()->map(function (Branch $branch, int $index) {
                return [
                    'sl' => $index + 1,
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'email' => $branch->email,
                    'type' => $branch->type,
                    'created_at' => (string) $branch->created_at,
                ];
            })->all(),
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return redirect()->back()->with('success', 'Branch deleted successfully!');
    }

    public function createReact(): Response
    {
        return Inertia::render('Auth/system/settings/branch/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'slug' => ['required', 'string', 'unique:branches,slug'],
            'address' => ['nullable', 'string'],
        ]);

        Branch::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'address' => $validated['address'] ?? null,
            'type' => 'Other',
        ]);

        return redirect()->route('system.branches.index')->with('success', 'Branch created successfully!');
    }

    public function editReact(Branch $branch): Response
    {
        return Inertia::render('Auth/system/settings/branch/Edit', [
            'branch' => [
                'id' => $branch->id,
                'name' => $branch->name,
                'email' => $branch->email,
                'phone' => $branch->phone,
                'slug' => $branch->slug,
                'address' => $branch->address,
            ],
        ]);
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'slug' => ['required', 'string', 'unique:branches,slug,' . $branch->id],
            'address' => ['nullable', 'string'],
        ]);

        $branch->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Branch updated successfully!');
    }
}
