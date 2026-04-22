<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slider as sliderModel;
use App\Models\Slider_has_slide;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryProductsController extends Controller
{
    public function index(Request $request, $cat)
    {
        $sort = $request->string('sort')->toString() ?: 'desc';
        $limit = max(20, (int) $request->input('limit', 20));
        $search = trim((string) $request->input('search', ''));

        $category = Category::query()
            ->with('children.children.children')
            ->where('slug', $cat)
            ->firstOrFail();

        $ids = [];
        $this->collectCategoryIds($category, $ids);

        $productsQuery = Product::query()
            ->whereIn('category_id', $ids)
            ->where([
                'belongs_to_type' => 'reseller',
                'status' => 'Active',
            ]);

        if ($search !== '') {
            $productsQuery->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%');
            });
        }

        $products = $productsQuery
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
            ]);

        $slider = sliderModel::query()
            ->where(['status' => true])
            ->whereNot('placement', '=', 'apps')
            ->orderBy('id', 'desc')
            ->get('id')
            ->pluck('id');

        $slides = Slider_has_slide::query()
            ->whereIn('slider_id', $slider)
            ->get([
                'id',
                'image',
                'main_title',
                'description',
                'action_url',
                'action_target',
                'title_color',
                'des_color',
                'action_text',
            ]);

        return Inertia::render('Products/CategoryIndex', [
            'cat' => $cat,
            'products' => $products,
            'categories' => Category::getAll(),
            'slides' => $slides,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'limit' => $limit,
            ],
            'loadMore' => (clone $productsQuery)->count() > $limit,
        ]);
    }

    private function collectCategoryIds(Category $category, array &$ids): void
    {
        $ids[] = $category->id;

        foreach ($category->children as $child) {
            $this->collectCategoryIds($child, $ids);
        }
    }
}
