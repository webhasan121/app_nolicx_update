<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Reseller_resel_product;
use App\Models\Category;
use App\Models\product_has_attribute;
use App\Models\product_has_image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use App\HandleImageUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductsController extends Controller
{
    use HandleImageUpload;

    public function index(Request $request): Response
    {
        $nav = $request->query('nav', 'own');
        $pd = $request->query('pd', 'Active');
        $search = trim((string) $request->query('search', ''));
        $data = $this->buildIndexQuery($nav, $pd, $search)
            ->paginate(config('app.paginate'))
            ->withQueryString();

        $products = $data->getCollection()->load(['orders' => function ($query) {
            $query->select('id', 'product_id', 'status')->orderBy('id');
        }]);

        return Inertia::render('Reseller/Products/Index', [
            'filters' => [
                'nav' => $nav,
                'pd' => $pd,
                'search' => $search,
            ],
            'products' => [
                'data' => $products->map(function (Product $product) {
                    $orders = $product->orders ?? collect();
                    $firstOrder = $orders->first();
                    $hasPending = $orders->where('status', 'Pending')->isNotEmpty();
                    $hasAccept = $orders->where('status', 'Accept')->isNotEmpty();

                    return [
                        'id' => $product->id,
                        'encrypted_id' => encrypt($product->id),
                        'thumbnail' => $product->thumbnail,
                        'unit' => $product->unit,
                        'name' => $product->name ?? 'N/A',
                        'status_label' => $product->status ? 'Active' : 'In Active',
                        'orders_count' => $orders->count(),
                        'first_order_id' => $firstOrder?->id,
                        'has_pending' => $hasPending,
                        'has_accept' => $hasAccept,
                        'buying_price' => $product->buying_price,
                        'price' => $product->price,
                        'offer_type' => (bool) $product->offer_type,
                        'discount' => $product->discount,
                        'created_at_human' => $product->created_at?->diffForHumans() ?? 'N/A',
                    ];
                })->values()->all(),
                'links' => $data->linkCollection()->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => $link['label'],
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ],
            'printUrl' => route('reseller.products.print', [
                'nav' => $nav,
                'pd' => $pd,
                'search' => $search,
            ]),
        ]);
    }

    public function print(Request $request): Response
    {
        $nav = $request->query('nav', 'own');
        $pd = $request->query('pd', 'Active');
        $search = trim((string) $request->query('search', ''));

        $products = $this->buildIndexQuery($nav, $pd, $search)
            ->get()
            ->load(['orders' => function ($query) {
                $query->select('id', 'product_id', 'status')->orderBy('id');
            }]);

        return Inertia::render('Reseller/Products/Print', [
            'filters' => [
                'nav' => $nav,
                'pd' => $pd,
                'search' => $search,
            ],
            'products' => $products->values()->map(function (Product $product, int $index) {
                $orders = $product->orders ?? collect();

                return [
                    'sl' => $index + 1,
                    'id' => $product->id,
                    'name' => $product->name ?? 'N/A',
                    'status_label' => $product->status ? 'Active' : 'In Active',
                    'orders_count' => $orders->count(),
                    'buying_price' => $product->buying_price,
                    'price' => $product->price,
                    'sell_price' => $product->offer_type ? $product->discount : $product->price,
                    'unit' => $product->unit,
                    'created_at_human' => $product->created_at?->diffForHumans() ?? 'N/A',
                ];
            })->all(),
        ]);
    }

    private function buildIndexQuery(string $nav, string $pd, string $search): Builder|HasMany
    {
        if ($nav === 'resel') {
            $ids = Reseller_resel_product::where(['user_id' => Auth::id()])->pluck('product_id');
            $query = Product::query()->whereIn('id', $ids)->latest('id');
        } elseif ($pd === 'Trash') {
            $query = auth()->user()->myProducts()->onlyTrashed()->latest('id');
        } else {
            $query = auth()->user()->myProducts()->where(['status' => $pd])->latest('id');
        }

        if ($search !== '') {
            $query->where(function ($productQuery) use ($search) {
                $productQuery
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    public function edit(string $id): Response
    {
        $productId = decrypt($id);
        $data = auth()->user()
            ?->myProducts()
            ->withTrashed()
            ->with(['category', 'showcase', 'attr', 'isResel'])
            ->findOrFail($productId);

        return Inertia::render('Reseller/Products/Edit', [
            'productData' => [
                'id' => $data->id,
                'encrypted_id' => encrypt($data->id),
                'user_id' => $data->user_id,
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
                'related_images' => $data->showcase->map(fn ($image) => [
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

    public function update(Request $request, string $id)
    {
        $productId = decrypt($id);
        $data = auth()->user()
            ?->myProducts()
            ->withTrashed()
            ->with(['attr', 'showcase'])
            ->findOrFail($productId);

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
        $data->meta_title = $payload['meta_title'] ?? $data->meta_title;
        $data->meta_description = $payload['meta_description'] ?? $data->meta_description;
        $data->keyword = $payload['keyword'] ?? $data->keyword;
        $data->meta_tags = $payload['meta_tags'] ?? $data->meta_tags;

        if ($request->file('newseothumb')) {
            $data->meta_thumbnail = $this->handleImageUpload(
                $request->file('newseothumb'),
                'products-seo',
                $data->meta_thumbnail
            );
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

    public function restore(string $id)
    {
        $productId = decrypt($id);
        $data = auth()->user()?->myProducts()->withTrashed()->findOrFail($productId);
        $data->restore();

        return redirect()->back()->with('success', 'Restore From Trash');
    }

    public function trash(string $id)
    {
        $productId = decrypt($id);
        $data = auth()->user()?->myProducts()->findOrFail($productId);
        $data->delete();

        return redirect()->back()->with('success', 'Product moved to trashed');
    }

    public function destroyImage(string $id, int $image)
    {
        $productId = decrypt($id);
        $data = auth()->user()?->myProducts()->withTrashed()->with('showcase')->findOrFail($productId);
        $img = $data->showcase->find($image);

        if ($img) {
            $path = $img->image;
            $img->delete();
            Storage::disk('public')->delete($path);
        }

        return redirect()->back()->with('success', 'Image Deletd !');
    }
}
