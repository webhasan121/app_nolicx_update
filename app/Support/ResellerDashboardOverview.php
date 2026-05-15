<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use App\Models\Reseller_resel_product;
use App\Models\User;

class ResellerDashboardOverview
{
    public static function get(User $user): array
    {
        $myProducts = $user->myProducts();

        return [
            'tp' => (clone $myProducts)->count(),
            'vendor' => Reseller_resel_product::query()
                ->where('user_id', $user->id)
                ->whereNotNull('belongs_to')
                ->distinct('belongs_to')
                ->count('belongs_to'),
            'category' => Category::count(),
            'products' => Product::where([
                'belongs_to_type' => 'vendor',
                'status' => 'Active',
            ])->limit(50)->get([
                'id',
                'name',
                'price',
                'discount',
                'offer_type',
                'thumbnail',
            ])->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'offer_type' => $product->offer_type,
                    'thumbnail' => $product->thumbnail,
                ];
            })->values()->all(),
            'categories' => Category::getAll()
                ->map(fn (Category $category) => self::mapCategory($category))
                ->values()
                ->all(),
        ];
    }

    private static function mapCategory(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'children' => $category->children
                ? $category->children->map(fn (Category $child) => self::mapCategory($child))->values()->all()
                : [],
        ];
    }
}
