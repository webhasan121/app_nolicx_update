<?php

namespace App\Http\Controllers\Reseller;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoriesController extends Controller
{
    use HandleImageUpload;

    public function index(): Response
    {
        $categories = Category::query()
            ->where([
                'user_id' => auth()->id(),
                'belongs_to' => 'reseller',
            ])
            ->withCount('products')
            ->latest('id')
            ->get();

        return Inertia::render('Reseller/Categories/Index', [
            'categories' => $categories->map(function (Category $item, int $index) {
                return [
                    'sl' => $index + 1,
                    'id' => $item->id,
                    'name' => $item->name ?? 'N/A',
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
        $flash = [];

        if (!auth()->user()->can('category_add')) {
            $flash['warning'] = 'You are not able to add category. ';
        }

        $payload = $request->validate([
            'name' => ['required', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:100'],
        ]);

        Category::create([
            'name' => $payload['name'],
            'slug' => Str::slug($payload['name']),
            'image' => $this->handleImageUpload($request->file('image'), 'categories', null),
            'user_id' => auth()->id(),
            'belongs_to' => auth()->user()->account_type(),
        ]);

        return redirect()->back()->with($flash + ['success' => 'Category Created']);
    }
}
