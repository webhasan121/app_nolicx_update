<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Support\SystemSettings;
use App\Models\Product;
use App\Models\ProductSaveForLater;
use App\Models\Category;
use App\Models\productSalesIndex;
use App\Models\Static_slider;
use Illuminate\Support\Carbon;
use App\Models\Slider_has_slide;
use App\Models\Slider as sliderModel;

class WelcomeController extends Controller
{
    public function index()
    {
        $medicineCategoryIds = $this->categoryTreeIds('medicine', 'Medicine');
        $womenCategoryIds = $this->categoryTreeIds('womens-item', "Women's Item");
        $groceryCategoryIds = $this->categoryTreeIds('grocery-item', 'Grocery Item');
        $foodCategoryIds = $this->categoryTreeIds('food-items', 'Food Items');
        $megaDealsCategoryIds = $this->categoryTreeIds('mega-deals', 'Mega Deals');

        $products = Product::query()
            ->reseller()
            ->active()
            ->latest()
            ->limit(21)
            ->get();

        $categories = Category::getAll();

        $sliders = Static_slider::query()
            ->home()
            ->active()
            ->with('slides')
            ->get();


        $slider = sliderModel::query()
            ->where('status', true)
            ->whereNot('placement', 'apps')
            ->orderByDesc('id')
            ->pluck('id');

        $slides = Slider_has_slide::whereIn('slider_id', $slider)->get();




        return Inertia::render('Welcome', [
            'roles' => auth()->user() ? auth()->user()->roles->pluck('name') : [],
            'active_nav' => auth()->user() ? auth()->user()->active_nav : null,
            'products' => $products,
            'categories' => $categories,
            'ss' => $sliders,
            'slides' => $slides,
            'developer_percentage' => SystemSettings::get('DEVELOPER_PERCENTAGE', '0'),
            'management_percentage' => SystemSettings::get('MANAGEMENT_PERCENTAGE', '0'),
            'newProducts' => Product::select('id', 'name', 'price', 'thumbnail', 'slug')
                ->where('badge', 'new')
                ->latest()
                ->limit(12)
                ->get(),
            'todaysProducts' => Product::whereDate('created_at', Carbon::today())
                ->where('belongs_to_type', 'reseller')
                ->orderBy('vc')
                ->limit(20)
                ->get(),
            'recommended' => $this->forYouProducts(),
            'medicineProducts' => Product::query()
                ->reseller()
                ->active()
                ->whereIn('category_id', $medicineCategoryIds ?: [0])
                ->latest()
                ->limit(20)
                ->get(),
            'womenProducts' => Product::query()
                ->reseller()
                ->active()
                ->whereIn('category_id', $womenCategoryIds ?: [0])
                ->latest()
                ->limit(20)
                ->get(),
            'groceryProducts' => Product::query()
                ->reseller()
                ->active()
                ->whereIn('category_id', $groceryCategoryIds ?: [0])
                ->latest()
                ->limit(20)
                ->get(),
            'foodProducts' => Product::query()
                ->reseller()
                ->active()
                ->whereIn('category_id', $foodCategoryIds ?: [0])
                ->latest()
                ->limit(20)
                ->get(),
            'megaDealsProducts' => Product::query()
                ->reseller()
                ->active()
                ->whereIn('category_id', $megaDealsCategoryIds ?: [0])
                ->latest()
                ->limit(20)
                ->get(),
            'topSales' => Product::query()->reseller()->whereIn('id', productSalesIndex::query()->orderBy('total_sales', 'desc')->limit(20)->pluck('product_id'))->get()
        ]);
    }

    private function categoryTreeIds(string $slug, string $name): array
    {
        $category = Category::query()
            ->with('children.children.children')
            ->where(function ($query) use ($slug, $name) {
                $query->where('slug', $slug)
                    ->orWhere('name', $name);
            })
            ->first();

        if (!$category) {
            return [];
        }

        $ids = [];
        $this->collectCategoryIds($category, $ids);

        return $ids;
    }

    private function forYouProducts()
    {
        $columns = [
            'id',
            'name',
            'title',
            'slug',
            'thumbnail',
            'offer_type',
            'discount',
            'price',
            'unit',
        ];

        $savedIds = auth()->user()
            ? ProductSaveForLater::query()
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(20)
                ->pluck('product_id')
                ->values()
                ->all()
            : [];

        if (auth()->user() && !$savedIds) {
            return collect();
        }

        $savedProducts = collect();

        if ($savedIds) {
            $savedProducts = Product::query()
                ->reseller()
                ->active()
                ->whereIn('id', $savedIds)
                ->get($columns)
                ->sortBy(fn($product) => array_search($product->id, $savedIds, true))
                ->values();
        }

        if (auth()->user()) {
            return $savedProducts->take(20)->values();
        }

        $fallback = Product::query()
            ->reseller()
            ->active()
            ->home()
            ->when($savedProducts->isNotEmpty(), fn($query) => $query->whereNotIn('id', $savedProducts->pluck('id')))
            ->orderBy('vc')
            ->limit(max(0, 20 - $savedProducts->count()))
            ->get($columns);

        return $savedProducts->merge($fallback)->take(20)->values();
    }

    private function collectCategoryIds(Category $category, array &$ids): void
    {
        $ids[] = $category->id;

        foreach ($category->children as $child) {
            $this->collectCategoryIds($child, $ids);
        }
    }
}
