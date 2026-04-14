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
        $category = Category::query()
            ->with('children.children.children')
            ->where('slug', $cat)
            ->firstOrFail();

        $ids = [];
        $this->collectCategoryIds($category, $ids);

        $products = Product::query()
            ->whereIn('category_id', $ids)
            ->where([
                'belongs_to_type' => 'reseller',
                'status' => 'Active',
            ])
            ->paginate(20, [
                'id',
                'name',
                'title',
                'slug',
                'thumbnail',
                'offer_type',
                'discount',
                'price',
                'unit',
            ])->withQueryString();

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
            'filters' => $request->only('page'),
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
