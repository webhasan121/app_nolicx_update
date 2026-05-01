<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $limit = max(1, min((int) $request->input('limit', 20), 100));

        $cacheKey = 'api.categories.' . md5(json_encode([
            'search' => $search,
            'limit' => $limit,
        ]));

        $categories = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($search, $limit) {
            return Category::query()
                ->select('id', 'name', 'slug', 'image', 'belongs_to', 'description', 'status')
                ->with(['children' => function ($query) {
                    $query->select('id', 'name', 'slug', 'image', 'belongs_to', 'description', 'status')
                        ->orderBy('name');
                }])
                ->where(function ($query) {
                    $query->whereNull('belongs_to')
                        ->orWhere('belongs_to', false);
                })
                ->when($search !== '', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orderBy('name')
                ->limit($limit)
                ->get()
                ->map(fn (Category $category) => $this->categoryPayload($category))
                ->values();
        });

        return response()->json([
            'success' => true,
            'message' => 'Categories fetched',
            'data' => $categories,
        ]);
    }

    public function products(Request $request, string $category)
    {
        $search = trim((string) $request->input('search', ''));
        $sort = strtolower((string) $request->input('sort', 'desc')) === 'asc' ? 'asc' : 'desc';
        $limit = max(1, min((int) $request->input('limit', 20), 100));

        $cacheKey = 'api.category.products.' . md5(json_encode([
            'category' => $category,
            'search' => $search,
            'sort' => $sort,
            'limit' => $limit,
        ]));

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category, $search, $sort, $limit) {
            $categoryModel = Category::query()
                ->with('children.children.children')
                ->where('id', $category)
                ->orWhere('slug', $category)
                ->firstOrFail();

            $categoryIds = [];
            $this->collectCategoryIds($categoryModel, $categoryIds);

            $productsQuery = Product::query()
                ->whereIn('category_id', $categoryIds)
                ->where([
                    'belongs_to_type' => 'reseller',
                    'status' => 'Active',
                ])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('title', 'like', '%' . $search . '%');
                    });
                });

            return [
                'category' => $this->categoryPayload($categoryModel),
                'products' => $productsQuery
                    ->orderBy('id', $sort)
                    ->limit($limit)
                    ->get([
                        'id',
                        'name',
                        'title',
                        'slug',
                        'thumbnail',
                        'offer_type',
                        'discount',
                        'price',
                        'unit',
                        'category_id',
                    ])
                    ->map(fn (Product $product) => $this->productPayload($product))
                    ->values(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Category products fetched',
            'data' => $data,
        ]);
    }

    private function collectCategoryIds(Category $category, array &$ids): void
    {
        $ids[] = $category->id;

        foreach ($category->children as $child) {
            $this->collectCategoryIds($child, $ids);
        }
    }

    private function categoryPayload(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'image' => $category->image,
            'parent_id' => $category->belongs_to,
            'description' => $category->description,
            'status' => $category->status,
            'children' => $category->relationLoaded('children')
                ? $category->children->map(fn (Category $child) => $this->categoryPayload($child))->values()
                : [],
        ];
    }

    private function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'title' => $product->title,
            'slug' => $product->slug,
            'thumbnail' => $product->thumbnail,
            'offer_type' => $product->offer_type,
            'price' => $product->price,
            'discount' => $product->discount,
            'selling_price' => $product->totalPrice(),
            'unit' => $product->unit,
            'category_id' => $product->category_id,
        ];
    }
}
