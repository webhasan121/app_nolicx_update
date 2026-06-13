<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\reseller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $country = trim($request->string('country')->toString());

        $category = Category::query()
            ->where('name', 'like', '%' . $q . '%')
            ->get();

        if ($category->count() === 0) {
            $category = Category::getAll();
        }

        $shop = reseller::query()
            ->where(function ($query) use ($q) {
                $query->where('shop_name_en', 'like', '%' . $q . '%')
                    ->where(['status' => 'Active']);
            })
            ->when($country !== '', function ($query) use ($country) {
                $query->whereRaw('LOWER(country) = ?', [mb_strtolower($country)]);
            })
            ->get();

        $product = Product::query()
            ->where([
                'belongs_to_type' => 'reseller',
                'status' => 'Active',
            ])
            ->where(function ($query) use ($q) {
                $query->whereAny(['name', 'title'], 'like', '%' . $q . '%');
            })
            ->when($country !== '', function ($query) use ($country) {
                $query->whereRaw('LOWER(country) = ?', [mb_strtolower($country)]);
            })
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Search/Index', [
            'q' => $q,
            'country' => $country,
            'category' => $category,
            'product' => $product,
            'shop' => $shop,
        ]);
    }
}
