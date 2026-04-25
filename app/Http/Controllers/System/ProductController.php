<?php

namespace App\Http\Controllers\System;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\product_has_attribute;
use App\Models\product_has_image;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    use HandleImageUpload;

    public function indexReact(Request $request)
    {
        $filter = $request->input('filter', 'Active');
        $from = $request->input('from', 'all');
        $find = $request->input('find');
        $sd = $request->input('sd');
        $ed = $request->input('ed');
        $isIncludeResel = filter_var($request->input('isIncludeResel', true), FILTER_VALIDATE_BOOL);

        $query = Product::query()
            ->with(['owner'])
            ->withCount(['isResel', 'resel'])
            ->orderBy('id', 'desc');

        $this->applyProductFilters($query, $filter, $from, $find, $sd, $ed, $isIncludeResel);

        $products = $query
            ->paginate(config('app.paginate'))
            ->withQueryString();

        $products->through(function ($item) {
            $ownerName = match ($item->belongs_to_type) {
                'reseller' => $item->owner?->resellerShop()?->shop_name_en ?? $item->owner?->name,
                'vendor' => $item->owner?->vendorShop()?->shop_name_en ?? $item->owner?->name,
                default => $item->owner?->name ?? 'Deleted Owner',
            };

            $discountMeta = null;

            if ($item->offer_type && $item->price) {
                $discountMeta = [
                    'discount' => $item->discount,
                    'off_percent' => round((100 * ($item->price - $item->discount)) / $item->price, 0),
                ];
            }

            return [
                'id' => $item->id,
                'name' => $item->name ?? 'N/A',
                'slug' => $item->slug ?? '',
                'thumbnail' => $item->thumbnail ? asset('storage/' . $item->thumbnail) : null,
                'status' => $item->status ?? 'N/A',
                'owner_name' => $ownerName,
                'belongs_to_type' => $item->belongs_to_type,
                'is_resel_count' => $item->is_resel_count ?? 0,
                'resel_count' => $item->resel_count ?? 0,
                'price' => $item->price ?? 0,
                'discount_meta' => $discountMeta,
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
            ];
        });

        return Inertia::render('Auth/system/products/index', [
            'filters' => [
                'filter' => $filter,
                'from' => $from,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
                'isIncludeResel' => $isIncludeResel,
            ],
            'products' => [
                'data' => $products->items(),
                'links' => collect($products->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
            ],
            'printUrl' => route('system.products.print-summery', [
                'filter' => $filter,
                'from' => $from,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
                'isIncludeResel' => $isIncludeResel,
            ]),
        ]);
    }

    public function printReact(Request $request)
    {
        $filter = $request->input('filter', 'Active');
        $from = $request->input('from', 'all');
        $find = $request->input('find');
        $sd = $request->input('sd');
        $ed = $request->input('ed');
        $isIncludeResel = filter_var($request->input('isIncludeResel', true), FILTER_VALIDATE_BOOL);

        $query = Product::query()
            ->with(['owner'])
            ->withCount(['isResel', 'resel'])
            ->orderBy('id', 'desc');

        $this->applyProductFilters($query, $filter, $from, $find, $sd, $ed, $isIncludeResel);

        $products = $query->get()->map(function ($item) {
            $ownerName = match ($item->belongs_to_type) {
                'reseller' => $item->owner?->resellerShop()?->shop_name_en ?? $item->owner?->name,
                'vendor' => $item->owner?->vendorShop()?->shop_name_en ?? $item->owner?->name,
                default => $item->owner?->name ?? 'Deleted Owner',
            };

            $discountMeta = null;

            if ($item->offer_type && $item->price) {
                $discountMeta = [
                    'discount' => $item->discount,
                    'off_percent' => round((100 * ($item->price - $item->discount)) / $item->price, 0),
                ];
            }

            return [
                'id' => $item->id,
                'name' => $item->name ?? 'N/A',
                'slug' => $item->slug ?? '',
                'thumbnail' => $item->thumbnail ? asset('storage/' . $item->thumbnail) : null,
                'status' => $item->status ?? 'N/A',
                'owner_name' => $ownerName,
                'belongs_to_type' => $item->belongs_to_type,
                'is_resel_count' => $item->is_resel_count ?? 0,
                'resel_count' => $item->resel_count ?? 0,
                'price' => $item->price ?? 0,
                'discount_meta' => $discountMeta,
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
            ];
        })->values()->all();

        return Inertia::render('Auth/system/products/PrintSummery', [
            'products' => $products,
            'filters' => [
                'filter' => $filter,
                'from' => $from,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
                'isIncludeResel' => $isIncludeResel,
            ],
        ]);
    }

    public function editReact($product)
    {
        $data = Product::with(['category', 'showcase', 'attr', 'isResel'])->withTrashed()->findOrFail($product);

        return Inertia::render('Auth/system/products/Edit', [
            'productData' => [
                'id' => $data->id,
                'name' => $data->name,
                'title' => $data->title,
                'category_id' => $data->category_id,
                'buying_price' => $data->buying_price,
                'price' => $data->price,
                'discount' => $data->discount,
                'offer_type' => (bool) $data->offer_type,
                'display_at_home' => (bool) $data->display_at_home,
                'unit' => $data->unit,
                'description' => $data->description,
                'thumbnail' => $data->thumbnail,
                'thumbnail_url' => $data->thumbnail ? asset('storage/' . $data->thumbnail) : null,
                'video' => $data->video,
                'video_url' => $data->video ? asset('storage/' . $data->video) : null,
                'meta_title' => $data->meta_title,
                'meta_description' => $data->meta_description,
                'keyword' => $data->keyword,
                'meta_tags' => $data->meta_tags,
                'meta_thumbnail' => $data->meta_thumbnail,
                'meta_thumbnail_url' => $data->meta_thumbnail ? asset('storage/' . $data->meta_thumbnail) : null,
                'cod' => (bool) $data->cod,
                'courier' => (bool) $data->courier,
                'hand' => (bool) $data->hand,
                'shipping_in_dhaka' => $data->shipping_in_dhaka,
                'shipping_out_dhaka' => $data->shipping_out_dhaka,
                'shipping_note' => $data->shipping_note,
                'deleted_at' => $data->deleted_at,
                'status' => $data->status,
                'created_at_human' => $data->created_at?->diffForHumans(),
                'created_at_formatted' => $data->created_at?->toFormattedDateString(),
                'is_resel' => (bool) $data->isResel,
                'category_name' => $data->category?->name ?? 'N/A',
                'related_images' => $data->showcase->map(fn($image) => [
                    'id' => $image->id,
                    'image' => $image->image,
                    'url' => asset('storage/' . $image->image),
                ])->values()->all(),
                'attr' => [
                    'id' => $data->attr?->id,
                    'name' => $data->attr?->name ?? '',
                    'value' => $data->attr?->value ?? '',
                ],
            ],
            'categories' => Category::getAll()->toArray(),
        ]);
    }

    public function updateReact(Request $request, $product)
    {
        $data = Product::with(['attr', 'showcase'])->withTrashed()->findOrFail($product);

        $payload = $request->validate([
            'name' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
            'category_id' => ['nullable'],
            'buying_price' => ['nullable'],
            'price' => ['nullable'],
            'discount' => ['nullable'],
            'offer_type' => ['nullable', 'boolean'],
            'display_at_home' => ['nullable', 'boolean'],
            'unit' => ['nullable'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'meta_tags' => ['nullable', 'string'],
            'cod' => ['nullable', 'boolean'],
            'courier' => ['nullable', 'boolean'],
            'hand' => ['nullable', 'boolean'],
            'shipping_in_dhaka' => ['nullable'],
            'shipping_out_dhaka' => ['nullable'],
            'shipping_note' => ['nullable', 'string'],
            'attr_name' => ['nullable', 'string'],
            'attr_value' => ['nullable', 'string'],
            'thumb' => ['nullable', 'file', 'image'],
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm,mkv', 'max:51200'],
            'newseothumb' => ['nullable', 'file', 'image'],
            'newImage.*' => ['nullable', 'file', 'image'],
        ]);

        $data->name = $payload['name'] ?? $data->name;
        $data->title = $payload['title'] ?? $data->title;
        $data->category_id = $payload['category_id'] ?? $data->category_id;
        $data->buying_price = $payload['buying_price'] ?? $data->buying_price;
        $data->price = $payload['price'] ?? $data->price;
        $data->discount = $payload['discount'] ?? $data->discount;
        $data->offer_type = $request->boolean('offer_type');
        $data->display_at_home = $request->boolean('display_at_home');
        $data->unit = $payload['unit'] ?? $data->unit;
        $data->description = $payload['description'] ?? $data->description;
        $data->thumbnail = $this->handleImageUpload($request->file('thumb'), 'products', $data->thumbnail);
        $data->video = $this->handleImageUpload($request->file('video'), 'products-videos', $data->video);
        $data->meta_title = $payload['meta_title'] ?? $data->meta_title;
        $data->meta_description = $payload['meta_description'] ?? $data->meta_description;
        $data->keyword = $payload['keyword'] ?? $data->keyword;
        $data->meta_tags = $payload['meta_tags'] ?? $data->meta_tags;

        if ($request->file('newseothumb')) {
            $data->meta_thumbnail = $this->handleImageUpload($request->file('newseothumb'), 'products-seo', $data->meta_thumbnail);
        }

        $data->cod = $request->boolean('cod');
        $data->courier = $request->boolean('courier');
        $data->hand = $request->boolean('hand');
        $data->shipping_in_dhaka = $payload['shipping_in_dhaka'] ?? $data->shipping_in_dhaka;
        $data->shipping_out_dhaka = $payload['shipping_out_dhaka'] ?? $data->shipping_out_dhaka;
        $data->shipping_note = $payload['shipping_note'] ?? $data->shipping_note;
        $data->save();

        if ($data->attr) {
            $data->attr->update([
                'name' => $payload['attr_name'] ?? '',
                'value' => $payload['attr_value'] ?? '',
            ]);
        } elseif (($payload['attr_name'] ?? null) || ($payload['attr_value'] ?? null)) {
            product_has_attribute::create([
                'product_id' => $data->id,
                'name' => $payload['attr_name'] ?? '',
                'value' => $payload['attr_value'] ?? '',
            ]);
        }

        if ($request->hasFile('newImage')) {
            foreach ($request->file('newImage') as $image) {
                product_has_image::create([
                    'product_id' => $data->id,
                    'image' => $this->handleImageUpload($image, 'product-showcase'),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Product Updated !');
    }

    public function restore($product)
    {
        $data = Product::withTrashed()->findOrFail($product);
        $data->restore();

        return redirect()->back()->with('success', 'Restore From Trash');
    }

    public function trash($product)
    {
        $data = Product::findOrFail($product);
        $data->delete();

        return redirect()->back()->with('success', 'Product moved to trashed');
    }

    public function destroyImage($product, $image)
    {
        $data = Product::with('showcase')->withTrashed()->findOrFail($product);
        $img = $data->showcase->find($image);

        if ($img) {
            $path = $img->image;
            $img->delete();
            Storage::disk('public')->delete($path);
        }

        return redirect()->back()->with('success', 'Image Deletd !');
    }

    private function applyProductFilters($query, string $filter, string $from, ?string $find, ?string $sd, ?string $ed, bool $isIncludeResel): void
    {
        if ($from && $from !== 'all' && $from !== 'id') {
            $query->where(['belongs_to_type' => $from]);
        }

        if (!$isIncludeResel) {
            $query->whereDoesntHave('isResel');
        }

        if ($filter !== 'both') {
            $query->where(['status' => $filter]);
        }

        if (!empty($find)) {
            $query->where(function ($subQuery) use ($find, $from) {
                if (($from === 'vendor' || $from === 'reseller') && is_numeric($find)) {
                    $subQuery->orWhere('user_id', $find);
                }

                $subQuery
                    ->orWhere('id', 'like', '%' . $find . '%')
                    ->orWhere('name', 'like', '%' . $find . '%')
                    ->orWhere('slug', 'like', '%' . $find . '%')
                    ->orWhereHas('owner', function ($ownerQuery) use ($find) {
                        $ownerQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%')
                            ->orWhere('phone', 'like', '%' . $find . '%');
                    });
            });
        }

        $this->applyDateFilter($query, $sd, $ed);
    }

    private function applyDateFilter($query, ?string $sd, ?string $ed): void
    {
        if (!empty($sd) && !empty($ed)) {
            $start = Carbon::parse($sd)->startOfDay();
            $end = Carbon::parse($ed)->endOfDay();

            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $query->whereBetween('created_at', [$start, $end]);

            return;
        }

        if (!empty($sd)) {
            $query->whereBetween('created_at', [
                Carbon::parse($sd)->startOfDay(),
                Carbon::parse($sd)->endOfDay(),
            ]);

            return;
        }

        if (!empty($ed)) {
            $query->whereBetween('created_at', [
                Carbon::parse($ed)->startOfDay(),
                Carbon::parse($ed)->endOfDay(),
            ]);
        }
    }
}
