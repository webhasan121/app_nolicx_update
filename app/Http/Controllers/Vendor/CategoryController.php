<?php

namespace App\Http\Controllers\Vendor;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    use HandleImageUpload;

    public function edit(int $cat): Response
    {
        $item = auth()->user()->myCategory()->findOrFail($cat);

        return Inertia::render('Vendor/Categories/Edit', [
            'category' => [
                'id' => $item->id,
                'name' => $item->name ?? '',
                'image' => $item->image,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Vendor/Categories/Create');
    }

    public function index(): Response
    {
        $categories = auth()->user()
            ->myCategory()
            ->withCount('products')
            ->latest('id')
            ->get();

        return Inertia::render('Vendor/Categories/Index', [
            'categories' => $categories->map(function (Category $item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name ?? 'N/A',
                    'image' => $item->image,
                    'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                    'owner' => $item->user_id === auth()->id() ? 'You' : 'N/A',
                    'products_count' => $item->products_count ?? 0,
                    'created_at_human' => $item->created_at?->diffForHumans() ?? 'N/A',
                    'created_at_formatted' => $item->created_at?->toFormattedDateString() ?? 'N/A',
                ];
            })->values()->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->can('category_add')) {
            return redirect()->back()->with('error', 'You are not able to add category.');
        }

        $payload = $request->validate([
            'name' => ['required', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:1024'],
        ]);

        Category::create([
            'name' => $payload['name'],
            'slug' => Str::slug($payload['name']),
            'image' => $this->handleImageUpload($request->file('image'), 'categories', null),
            'user_id' => auth()->id(),
            'belongs_to' => auth()->user()->account_type(),
        ]);

        return redirect()->back()->with('success', 'Category Created');
    }

    public function destroy(int $category): RedirectResponse
    {
        $item = auth()->user()->myCategory()->findOrFail($category);
        $item->delete();

        return redirect()->back()->with('success', 'Category deleted !');
    }

    public function update(Request $request, int $cat): RedirectResponse
    {
        $item = auth()->user()->myCategory()->findOrFail($cat);

        $payload = $request->validate([
            'name' => ['required', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:1024'],
        ]);

        $item->update([
            'name' => $payload['name'],
            'slug' => Str::slug($payload['name']),
            'image' => $this->handleImageUpload($request->file('image'), 'categories', $item->image),
        ]);

        return redirect()->route('vendor.category.view')->with('success', 'Category updated');
    }
}

