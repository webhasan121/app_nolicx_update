<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use App\Models\vendor;

class ResellerDashboardOverview
{
    public static function get(): array
    {
        return [
            'tp' => Product::where(['belongs_to_type' => 'vendor'])->count(),
            'vendor' => vendor::count(),
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
        ];
    }
}