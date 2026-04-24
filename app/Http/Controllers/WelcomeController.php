<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Support\SystemSettings;
use App\Models\Product;
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
            'recommended' => Product::query()
                ->reseller()
                ->active()
                ->home()
                ->orderBy('vc')
                ->limit(20)
                ->get(),
            'topSales' => Product::query()->reseller()->whereIn('id', productSalesIndex::query()->orderBy('total_sales', 'desc')->limit(20)->pluck('product_id'))->get()
        ]);
    }
}
