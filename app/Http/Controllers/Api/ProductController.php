<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function show(string $product)
    {
        $cacheKey = 'api.product.details.' . $product;

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($product) {
            $item = Product::query()
                ->with([
                    'category:id,name,slug',
                    'attr:id,product_id,name,value',
                    'showcase:id,product_id,image',
                    'comments.user:id,name',
                    'owner:id,name',
                ])
                ->where([
                    'status' => 'Active',
                    'belongs_to_type' => 'reseller',
                ])
                ->where(function ($query) use ($product) {
                    $query->where('id', $product)
                        ->orWhere('slug', $product);
                })
                ->firstOrFail();

            $relatedProducts = Product::query()
                ->where([
                    'category_id' => $item->category_id,
                    'status' => 'Active',
                    'belongs_to_type' => 'reseller',
                ])
                ->where('id', '!=', $item->id)
                ->orderByDesc('id')
                ->limit(10)
                ->get($this->listColumns())
                ->map(fn (Product $product) => $this->productListPayload($product))
                ->values();

            return [
                'product' => $this->productDetailsPayload($item),
                'related_products' => $relatedProducts,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Product details fetched',
            'data' => $data,
        ]);
    }

    public function today(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $limit = max(1, min((int) $request->input('limit', 20), 100));

        $cacheKey = 'api.products.today.' . md5(json_encode([
            'search' => $search,
            'limit' => $limit,
            'date' => Carbon::today()->toDateString(),
        ]));

        $products = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($search, $limit) {
            return Product::query()
                ->whereDate('created_at', Carbon::today())
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
                })
                ->orderBy('vc')
                ->limit($limit)
                ->get($this->listColumns())
                ->map(fn (Product $product) => $this->productListPayload($product))
                ->values();
        });

        return response()->json([
            'success' => true,
            'message' => 'Today products fetched',
            'data' => $products,
        ]);
    }

    public function forYou(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $limit = max(1, min((int) $request->input('limit', 20), 100));

        $cacheKey = 'api.products.for_you.' . md5(json_encode([
            'search' => $search,
            'limit' => $limit,
        ]));

        $products = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($search, $limit) {
            return Product::query()
                ->reseller()
                ->active()
                ->home()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('title', 'like', '%' . $search . '%');
                    });
                })
                ->orderBy('vc')
                ->limit($limit)
                ->get($this->listColumns())
                ->map(fn (Product $product) => $this->productListPayload($product))
                ->values();
        });

        return response()->json([
            'success' => true,
            'message' => 'For you products fetched',
            'data' => $products,
        ]);
    }

    private function listColumns(): array
    {
        return [
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
            'vc',
        ];
    }

    private function productListPayload(Product $product): array
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
            'views' => $product->vc,
        ];
    }

    private function productDetailsPayload(Product $product): array
    {
        return [
            ...$this->productListPayload($product),
            'description' => $product->description,
            'video' => $product->video,
            'video_url' => $product->video ? asset('storage/' . $product->video) : null,
            'brand' => $product->brand,
            'country' => $product->country,
            'state' => $product->state,
            'cod' => $product->cod,
            'courier' => $product->courier ?? null,
            'hand' => $product->hand,
            'shipping_in_dhaka' => $product->shipping_in_dhaka ?? null,
            'shipping_out_dhaka' => $product->shipping_out_dhaka ?? null,
            'shipping_note' => $product->shipping_note,
            'badge' => $product->badge,
            'tags' => $product->tags,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
            'keyword' => $product->keyword,
            'meta_thumbnail' => $product->meta_thumbnail,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'attr' => $product->attr ? [
                'name' => $product->attr->name,
                'value' => $product->attr->value,
            ] : null,
            'showcase' => $product->showcase
                ->map(fn ($image) => [
                    'id' => $image->id,
                    'image' => $image->image,
                ])
                ->values(),
            'owner' => [
                'id' => $product->owner?->id,
                'name' => $product->owner?->name,
            ],
            'comments' => $product->comments
                ->sortByDesc('created_at')
                ->values()
                ->map(fn ($comment) => [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'comments' => $comment->comments,
                    'created_at_human' => $comment->created_at?->diffForHumans(),
                    'user' => [
                        'name' => $comment->user?->name,
                    ],
                ]),
        ];
    }
}
