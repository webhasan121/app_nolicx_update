<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\CountrySelection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductsIndexController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->string('sort')->toString() ?: 'desc';
        $limit = max(24, (int) $request->input('limit', 24));
        $search = trim((string) $request->input('search', ''));
        $country = CountrySelection::fromRequest($request);

        $productsQuery = Product::query()
            ->where([
                'belongs_to_type' => 'reseller',
                'status' => 'Active',
            ])
            ->when($country, fn($query) => CountrySelection::applyToProducts($query, $country));

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

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => Category::getAll(),
            'filters' => [
                'sort' => $sort,
                'search' => $search,
                'limit' => $limit,
                'country' => $country,
            ],
            'loadMore' => (clone $productsQuery)->count() > $limit,
        ]);
    }
}
