<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductsIndexController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->string('sort')->toString() ?: 'desc';
        $limit = max(1, (int) $request->input('limit', 50));

        $products = Product::query()
            ->where([
                'belongs_to_type' => 'reseller',
                'status' => 'Active',
            ])
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
                'search' => $request->input('search'),
                'limit' => $limit,
            ],
            'loadMore' => Product::count() > $limit,
        ]);
    }
}
