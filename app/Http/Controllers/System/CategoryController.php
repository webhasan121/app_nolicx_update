<?php

namespace App\Http\Controllers\System;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    use HandleImageUpload;

    public function indexReact(): Response
    {
        $categories = Category::getAll();

        return Inertia::render('Auth/system/categories/index', [
            'categories' => $categories->map(fn (Category $category) => $this->serializeCategory($category))->values()->all(),
            'categoryCount' => $categories->count(),
            'parentCategories' => $categories->map(fn (Category $category) => $this->serializeParentCategory($category))->values()->all(),
        ]);
    }

    public function editReact($cid): Response|RedirectResponse
    {
        $category = Category::find($cid);

        if (!$category) {
            return redirect()->route('system.categories.index');
        }

        $categories = Category::getAll();

        return Inertia::render('Auth/system/categories/Edit', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'belongs_to' => $category->belongs_to,
                'image' => $category->image,
            ],
            'parentCategories' => $categories->map(fn (Category $item) => $this->serializeParentCategory($item))->values()->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories,name|max:50',
            'image' => 'nullable|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'slug' => 'required|max:100|unique:categories,slug',
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'image' => $this->handleImageUpload($request->file('image'), 'categories', null),
            'user_id' => Auth::id(),
            'belongs_to' => $validated['parent_id'] ?: null,
        ]);

        return redirect()->route('system.categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with subcategories.');
        }

        if ($category->slug === 'default-category') {
            return redirect()->back()->with('error', 'Cannot delete the default category.');
        }

        if ($category->products->count() > 0) {
            $defaultCategory = Category::where('slug', 'default-category')->first();
            if ($defaultCategory) {
                $category->products()->update(['category_id' => $defaultCategory->id]);

                return redirect()->back()->with('success', 'Products moved to default category.');
            }

            return redirect()->back()->with('error', 'Default category not found. Cannot move products.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    public function update(Request $request, $cid): RedirectResponse
    {
        $category = Category::findOrFail($cid);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'belongs_to' => 'nullable|exists:categories,id',
            'newImage' => 'nullable|file|max:100',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'belongs_to' => $validated['belongs_to'] ?? null,
            'image' => $this->handleImageUpload($request->file('newImage'), 'categories', $category->image),
        ]);

        return redirect()->route('system.categories.index')->with('success', 'Category updated successfully.');
    }

    private function serializeCategory(Category $category): array
    {
        $category->loadMissing('children', 'products');

        return [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $category->image,
            'slug' => $category->slug,
            'products_count' => $category->products->count(),
            'children_count' => $category->children->count(),
            'children' => $category->children->map(fn (Category $child) => $this->serializeCategory($child))->values()->all(),
        ];
    }

    private function serializeParentCategory(Category $category): array
    {
        $category->loadMissing('children.children');

        return [
            'id' => $category->id,
            'name' => $category->name,
            'children' => $category->children->map(fn (Category $child) => [
                'id' => $child->id,
                'name' => $child->name,
                'children' => $child->children->map(fn (Category $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])->values()->all(),
            ])->values()->all(),
        ];
    }
}
