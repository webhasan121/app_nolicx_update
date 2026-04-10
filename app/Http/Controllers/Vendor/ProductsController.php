<?php

namespace App\Http\Controllers\Vendor;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\product_has_attribute;
use App\Models\product_has_image;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductsController extends Controller
{
    use HandleImageUpload;

    public function index(Request $request): Response
    {
        $filters = [
            'nav' => $request->query('nav', 'Active'),
            'take' => $request->query('take', ''),
            'search' => $request->query('search', ''),
            'created' => $request->query('created', ''),
        ];

        $query = auth()->user()->myProducts()->latest('id');

        if ($filters['take'] === 'trash') {
            $query->onlyTrashed();
        } else {
            if (($filters['nav'] ?? 'Active') === 'Active') {
                $query->where(function ($q) {
                    $q->where('status', 'Active')
                        ->orWhere('status', 1)
                        ->orWhere('status', true);
                });
            } elseif (($filters['nav'] ?? '') === 'In Active') {
                $query->where(function ($q) {
                    $q->where('status', 'In Active')
                        ->orWhere('status', 'Disable')
                        ->orWhere('status', 'Disabled')
                        ->orWhere('status', 'Drafted')
                        ->orWhere('status', 0)
                        ->orWhere('status', false);
                });
            }
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['created'] === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        $products = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Vendor/Products/Index', [
            'filters' => $filters,
            'products' => [
                'data' => $products->getCollection()->map(function (Product $product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name ?? 'N/A',
                        'thumbnail' => $product->thumbnail,
                        'thumbnail_url' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                        'unit' => $product->unit,
                        'buying_price' => $product->buying_price,
                        'price' => $product->price,
                        'discount' => $product->discount ?? 0,
                        'status' => $product->status ? 'Active' : 'In Active',
                        'created_at_human' => $product->created_at?->diffForHumans() ?? 'N/A',
                        'encrypted_id' => encrypt($product->id),
                    ];
                })->values()->all(),
                'links' => $products->linkCollection()->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => $link['label'],
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
            ],
            'printUrl' => route('vendor.products.print', [
                'nav' => $filters['nav'],
                'take' => $filters['take'],
                'search' => $filters['search'],
                'created' => $filters['created'],
            ]),
            'selectedCount' => 0,
            'isReseller' => auth()->user()->hasRole('reseller'),
        ]);
    }

    public function print(Request $request): Response
    {
        $filters = [
            'nav' => $request->query('nav', 'Active'),
            'take' => $request->query('take', ''),
            'search' => $request->query('search', ''),
            'created' => $request->query('created', ''),
        ];

        $query = auth()->user()->myProducts()->latest('id');

        if ($filters['take'] === 'trash') {
            $query->onlyTrashed();
        } else {
            if (($filters['nav'] ?? 'Active') === 'Active') {
                $query->where(function ($q) {
                    $q->where('status', 'Active')
                        ->orWhere('status', 1)
                        ->orWhere('status', true);
                });
            } elseif (($filters['nav'] ?? '') === 'In Active') {
                $query->where(function ($q) {
                    $q->where('status', 'In Active')
                        ->orWhere('status', 'Disable')
                        ->orWhere('status', 'Disabled')
                        ->orWhere('status', 'Drafted')
                        ->orWhere('status', 0)
                        ->orWhere('status', false);
                });
            }
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['created'] === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        $products = $query->get();

        return Inertia::render('Vendor/Products/Print', [
            'filters' => $filters,
            'products' => $products->values()->map(function (Product $product, int $index) {
                return [
                    'sl' => $index + 1,
                    'id' => $product->id,
                    'name' => $product->name ?? 'N/A',
                    'unit' => $product->unit,
                    'buying_price' => $product->buying_price,
                    'price' => $product->price,
                    'discount' => $product->discount ?? 0,
                    'status' => $product->status ? 'Active' : 'In Active',
                    'created_at_human' => $product->created_at?->diffForHumans() ?? 'N/A',
                ];
            })->all(),
        ]);
    }

    public function bulkTrash(Request $request): RedirectResponse
    {
        $ids = collect($request->input('selectedModel', []))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        if (count($ids) > 0) {
            auth()->user()->myProducts()->whereIn('id', $ids)->delete();
            return back()->with('success', 'Product Move to Trash');
        }

        return back();
    }

    public function bulkRestore(Request $request): RedirectResponse
    {
        $ids = collect($request->input('selectedModel', []))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        if (count($ids) > 0) {
            auth()->user()->myProducts()->onlyTrashed()->whereIn('id', $ids)->restore();
            return back()->with('success', 'Product restore from Trash');
        }

        return back();
    }

    public function create(Request $request): Response|RedirectResponse
    {
        $roles = auth()->user()->getRoleNames();
        $belongsTo = count($roles) > 2
            ? auth()->user()->active_nav
            : (auth()->user()->isVendor() ? 'vendor' : 'reseller');

        $shop = null;
        if ($belongsTo === 'reseller') {
            $shop = auth()->user()->resellerShop();
        }
        if ($belongsTo === 'vendor') {
            $shop = auth()->user()->vendorShop();
        }

        if (!$shop) {
            return redirect()->route('vendor.shops.create');
        }

        $ableToCreate = auth()->user()->myProducts()->count() < ($shop->max_product_upload ?? 0);

        return Inertia::render('Vendor/Products/Create', [
            'categories' => Category::getAll(),
            'shop' => [
                'system_get_comission' => $shop->system_get_comission ?? 'N/A',
                'max_product_upload' => $shop->max_product_upload ?? 0,
            ],
            'ableToCreate' => $ableToCreate,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|min:5',
            'title' => 'required|min:5|max:255',
            'category_id' => 'required',
            'buying_price' => 'required',
            'price' => 'required',
            'thumb' => 'required|image|max:4096',
            'newImage.*' => 'image|max:2048',
        ]);

        $roles = auth()->user()->getRoleNames();
        $belongsTo = count($roles) > 2
            ? auth()->user()->active_nav
            : (auth()->user()->isVendor() ? 'vendor' : 'reseller');

        $shop = $belongsTo === 'vendor'
            ? auth()->user()->vendorShop()
            : auth()->user()->resellerShop();

        if (!$shop) {
            return redirect()->route('vendor.shops.create');
        }

        if (auth()->user()->myProducts()->count() >= ($shop->max_product_upload ?? 0)) {
            return redirect()
                ->back()
                ->with('error', 'You have reached the maximum number of products allowed for your shop.');
        }

        $data = [
            'name' => $request->input('name'),
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
            'buying_price' => $request->input('buying_price'),
            'price' => $request->input('price'),
            'unit' => $request->input('unit'),
            'offer_type' => $request->boolean('offer_type'),
            'discount' => $request->input('discount'),
            'display_at_home' => $request->boolean('display_at_home') ? now() : null,
            'cod' => $request->boolean('cod'),
            'courier' => $request->boolean('courier'),
            'hand' => $request->boolean('hand'),
            'shipping_in_dhaka' => $request->input('shipping_in_dhaka'),
            'shipping_out_dhaka' => $request->input('shipping_out_dhaka'),
            'shipping_note' => $request->input('shipping_note'),
            'description' => $request->input('description'),
            'slug' => Str::slug($request->input('title')),
            'thumbnail' => $this->handleImageUpload($request->file('thumb'), 'products', null),
            'belongs_to_type' => $belongsTo,
            'country' => Auth::user()->country ?? 'Bangladesh',
            'state' => Auth::user()->state ?? null,
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'meta_tags' => $request->input('meta_tags'),
            'keyword' => $request->input('meta_keyword'),
            'meta_thumbnail' => $this->handleImageUpload($request->file('meta_thumbnail'), 'products-seo', ''),
        ];

        try {
            $product = Product::create($data);

            if ($product->id && $request->filled('attr_name')) {
                product_has_attribute::create([
                    'product_id' => $product->id,
                    'name' => $request->input('attr_name'),
                    'value' => $request->input('attr_value'),
                ]);
            }

            if ($product->id && $request->file('newImage')) {
                foreach ($request->file('newImage') as $image) {
                    product_has_image::create([
                        'product_id' => $product->id,
                        'image' => $this->handleImageUpload($image, 'product-showcase', null),
                    ]);
                }
            }

            return redirect()
                ->route('vendor.products.edit', ['product' => encrypt($product->id)])
                ->with('success', 'Product Created');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Have an error to upload product.');
        }
    }

    public function edit(string $product): Response
    {
        $productId = decrypt($product);
        $data = auth()->user()
            ?->myProducts()
            ->withTrashed()
            ->with(['category', 'showcase', 'attr', 'isResel'])
            ->findOrFail($productId);

        return Inertia::render('Vendor/Products/Edit', [
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

    public function update(Request $request, string $product): RedirectResponse
    {
        $productId = decrypt($product);
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

    public function restore(string $product): RedirectResponse
    {
        $productId = decrypt($product);
        $data = auth()->user()?->myProducts()->withTrashed()->findOrFail($productId);
        $data->restore();

        return redirect()->back()->with('success', 'Restore From Trash');
    }

    public function trash(string $product): RedirectResponse
    {
        $productId = decrypt($product);
        $data = auth()->user()?->myProducts()->findOrFail($productId);
        $data->delete();

        return redirect()->back()->with('success', 'Product moved to trashed');
    }

    public function destroyImage(string $product, int $image): RedirectResponse
    {
        $productId = decrypt($product);
        $data = auth()->user()?->myProducts()->withTrashed()->with('showcase')->findOrFail($productId);
        $img = $data->showcase->find($image);

        if ($img) {
            $path = $img->image;
            $img->delete();
            Storage::disk('public')->delete($path);
        }

        return redirect()->back()->with('success', 'Image Deletd !');
    }

    public function resell(Request $request): Response|RedirectResponse
    {
        $encryptedProduct = $request->query('product');
        if (!$encryptedProduct) {
            return redirect()->route('vendor.products.view')->with('error', 'Product not found.');
        }

        try {
            $productId = decrypt($encryptedProduct);
        } catch (DecryptException $e) {
            return redirect()->route('vendor.products.view')->with('error', 'Invalid product identifier.');
        }

        $product = Product::with(['resel.mainProduct', 'resel.reselProduct', 'isResel.owner'])->find($productId);
        if (!$product) {
            return redirect()->route('vendor.products.view')->with('error', 'Product not found.');
        }

        $act = auth()->user()->account_type();

        return Inertia::render('Vendor/Products/Resell', [
            'act' => $act,
            'productData' => [
                'id' => $product->id,
                'encrypted_id' => encrypt($product->id),
                'title' => $product->title,
                'resel_count' => $product->resel?->count() ?? 0,
                'resel_from_shop' => $product->isResel?->owner?->vendorShop()?->shop_name_en,
                'resel_from_date' => $product->isResel?->created_at?->toFormattedDateString(),
            ],
            'rows' => ($product->resel ?? collect())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                    'user_id' => $item->user_id,
                    'main_price' => $item->mainProduct?->price ?? 0,
                    'reseller_price' => $item->reselProduct?->price ?? 0,
                ];
            })->values()->all(),
        ]);
    }
}
