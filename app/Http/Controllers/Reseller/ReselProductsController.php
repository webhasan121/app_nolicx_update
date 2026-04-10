<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductComissionController;
use App\Models\CartOrder;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\product_has_image;
use App\Models\Reseller_resel_product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReselProductsController extends Controller
{
    public function index(Request $request): Response
    {
        $cat = $request->query('cat');
        $categoryIds = [];

        if ($cat) {
            $category = Category::find($cat);
            if ($category) {
                $this->collectCategoryIds($category, $categoryIds);
            }
        }

        $productsQuery = Product::query()
            ->where(['belongs_to_type' => 'vendor', 'status' => 'Active'])
            ->with(['attr:id,product_id,name,value', 'category:id,name'])
            ->orderByDesc('id');

        if (!empty($categoryIds)) {
            $productsQuery->whereIn('category_id', $categoryIds);
        }

        $products = $productsQuery->paginate(50)->withQueryString();

        $shop = auth()->user()?->resellerShop();
        $totalReselProducts = Reseller_resel_product::where(['user_id' => Auth::id()])->count();
        $ableToAdd = false;

        if ($shop && $shop->allow_max_resell_product) {
            $ableToAdd = $totalReselProducts < $shop->max_resell_product;
        }

        return Inertia::render('Reseller/Resel/Products/Index', [
            'filters' => [
                'cat' => $cat,
            ],
            'shop' => $shop ? [
                'max_resell_product' => $shop->max_resell_product,
                'allow_max_resell_product' => (bool) $shop->allow_max_resell_product,
            ] : null,
            'totalReselProducts' => $totalReselProducts,
            'ableToAdd' => $ableToAdd,
            'categories' => $this->buildCategoryTree(),
            'products' => [
                'data' => $products->getCollection()->map(function (Product $product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'title' => $product->title,
                        'thumbnail' => $product->thumbnail,
                        'thumbnail_url' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'offer_type' => (bool) $product->offer_type,
                        'unit' => $product->unit,
                        'total_price' => $product->totalPrice(),
                        'attr' => $product->attr ? [
                            'name' => $product->attr->name,
                            'value' => $product->attr->value,
                        ] : null,
                        'shipping_in_dhaka' => $product->shipping_in_dhaka,
                        'shipping_out_dhaka' => $product->shipping_out_dhaka,
                        'category' => $product->category ? [
                            'id' => $product->category->id,
                            'name' => $product->category->name,
                        ] : null,
                    ];
                })->values()->all(),
                'links' => $products->linkCollection()->toArray(),
                'prev_page_url' => $products->previousPageUrl(),
                'next_page_url' => $products->nextPageUrl(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function order(Request $request, Product $product)
    {
        $product = Product::query()
            ->where('id', $product->id)
            ->where(['belongs_to_type' => 'vendor', 'status' => 'Active'])
            ->with('attr:id,product_id,name,value')
            ->firstOrFail();

        $rules = [
            'name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'district' => ['required', 'string'],
            'upozila' => ['required', 'string'],
            'location' => ['required', 'string'],
            'house_no' => ['nullable', 'string'],
            'road_no' => ['nullable', 'string'],
            'area_condition' => ['required', 'string'],
            'delevery' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'attr' => ['required', 'string'],
        ];

        $data = $request->validate($rules);

        $shipping = $data['area_condition'] === 'Dhaka'
            ? ($product->shipping_in_dhaka ?? 0)
            : ($product->shipping_out_dhaka ?? 0);

        $order = Order::create([
            'user_id' => Auth::id(),
            'user_type' => 'reseller',
            'belongs_to' => $product->user_id,
            'belongs_to_type' => 'vendor',
            'quantity' => $data['quantity'],
            'total' => $data['quantity'] * $product->totalPrice(),
            'status' => 'Pending',
            'name' => 'Purchase',
            'district' => $data['district'],
            'upozila' => $data['upozila'],
            'location' => $data['location'],
            'house_no' => $data['house_no'] ?? null,
            'road_no' => $data['road_no'] ?? null,
            'area_condition' => $data['area_condition'],
            'delevery' => $data['delevery'],
            'number' => $data['phone'],
            'shipping' => $shipping,
        ]);

        CartOrder::create([
            'user_id' => Auth::id(),
            'user_type' => 'reseller',
            'belongs_to' => $product->user_id,
            'belongs_to_type' => 'vendor',
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'price' => $product->totalPrice(),
            'size' => $data['attr'],
            'total' => $data['quantity'] * $product->totalPrice(),
            'buying_price' => $product->buying_price ?? 0,
            'status' => 'Pending',
        ]);

        ProductComissionController::dispatchProductComissionsListeners($order->id);

        return back()->with('success', 'Order Done');
    }

    public function show(string $pd): Response
    {
        $product = Product::query()
            ->where(['belongs_to_type' => 'vendor', 'status' => 'Active'])
            ->with(['category', 'attr', 'showcase', 'owner'])
            ->findOrFail($pd);

        $shop = auth()->user()?->resellerShop();
        $totalReselProducts = Reseller_resel_product::where(['user_id' => Auth::id()])->count();
        $ableToAdd = false;

        if ($shop && $shop->allow_max_resell_product) {
            $ableToAdd = $totalReselProducts < $shop->max_resell_product;
        }

        $basePrice = $product->offer_type
            ? ($product->price ?? 0)
            : ($product->discount ?? $product->price ?? 0);

        $reselPrice = $basePrice + 150;
        $reselDiscountPrice = $basePrice + 100;

        $owner = $product->owner;
        $vendorShop = $owner?->isVendor();

        return Inertia::render('Reseller/Resel/Products/View', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'title' => $product->title,
                'description' => $product->description,
                'thumbnail' => $product->thumbnail,
                'thumbnail_url' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                'price' => $product->price,
                'discount' => $product->discount,
                'offer_type' => (bool) $product->offer_type,
                'unit' => $product->unit,
                'total_price' => $product->totalPrice(),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                ] : null,
                'attr' => $product->attr ? [
                    'name' => $product->attr->name,
                    'value' => $product->attr->value,
                ] : null,
                'showcase' => $product->showcase->map(fn ($image) => [
                    'id' => $image->id,
                    'image' => $image->image,
                    'url' => asset('storage/' . $image->image),
                ])->values()->all(),
                'owner' => [
                    'id' => $owner?->id,
                    'name' => $owner?->name,
                    'email' => $vendorShop?->email ?? $owner?->email,
                    'phone' => $vendorShop?->phone ?? $owner?->phone,
                    'address' => $vendorShop?->address,
                    'shop' => $vendorShop ? [
                        'id' => $vendorShop->id,
                        'slug' => $vendorShop->slug,
                        'shop_name_en' => $vendorShop->shop_name_en,
                        'shop_name_bn' => $vendorShop->shop_name_bn,
                    ] : null,
                ],
            ],
            'reselDefaults' => [
                'resel_price' => $reselPrice,
                'resel_discount_price' => $reselDiscountPrice,
            ],
            'shop' => $shop ? [
                'max_resell_product' => $shop->max_resell_product,
                'allow_max_resell_product' => (bool) $shop->allow_max_resell_product,
            ] : null,
            'totalReselProducts' => $totalReselProducts,
            'ableToAdd' => $ableToAdd,
            'categories' => $this->buildCategoryTree(),
        ]);
    }

    public function clone(Request $request, Product $product)
    {
        $product = Product::query()
            ->where(['belongs_to_type' => 'vendor', 'status' => 'Active'])
            ->with(['showcase'])
            ->findOrFail($product->id);

        $exists = Reseller_resel_product::where([
            'parent_id' => $product->id,
            'user_id' => Auth::id(),
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Already Cloned !');
        }

        $payload = $request->validate([
            'resel_price' => ['required', 'numeric'],
            'resel_discount_price' => ['nullable', 'numeric'],
            'is_resel_with_discount_price' => ['nullable', 'boolean'],
            'reseller_category_id' => ['required'],
        ]);

        $isWithDiscount = $request->boolean('is_resel_with_discount_price');

        if ($isWithDiscount && empty($payload['resel_discount_price'])) {
            return back()->withErrors(['resel_discount_price' => 'Discount price required']);
        }

        $newProduct = Product::create([
            'name' => $product->name,
            'title' => $product->title,
            'slug' => $product->slug,
            'description' => $product->description,
            'thumbnail' => $product->thumbnail,
            'price' => $payload['resel_price'],
            'discount' => $isWithDiscount ? $payload['resel_discount_price'] : null,
            'offer_type' => $isWithDiscount ? 1 : 0,
            'buying_price' => $product->totalPrice(),
            'category_id' => $payload['reseller_category_id'],
            'belongs_to_type' => 'reseller',
            'user_id' => Auth::id(),
            'unit' => $product->unit ?? 1,
            'status' => 'Active',
            'country' => auth()->user()->country ?? 'Bangladesh',
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
            'meta_tags' => $product->meta_tags,
            'keyword' => $product->keyword,
            'meta_thumbnail' => $product->meta_thumbnail,
        ]);

        $rrp = new Reseller_resel_product();
        $rrp->forceFill([
            'user_id' => Auth::id(),
            'belongs_to' => $product->user_id,
            'product_id' => $newProduct->id,
            'parent_id' => $product->id,
        ]);
        $rrp->save();

        if ($rrp) {
            foreach ($product->showcase as $value) {
                product_has_image::create([
                    'product_id' => $newProduct->id,
                    'image' => $value->image,
                ]);
            }
        }

        return back()->with('success', 'Successfully cloned! Product added to your list');
    }

    private function collectCategoryIds(Category $category, array &$ids): void
    {
        $ids[] = $category->id;

        foreach ($category->children as $child) {
            $this->collectCategoryIds($child, $ids);
        }
    }

    private function buildCategoryTree(): array
    {
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug', 'belongs_to']);
        $byParent = [];

        foreach ($categories as $category) {
            $parentId = $category->belongs_to ?? 0;
            $byParent[$parentId][] = $category;
        }

        $build = function ($parentId) use (&$build, $byParent) {
            $items = $byParent[$parentId] ?? [];

            return array_map(function (Category $category) use (&$build) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'children' => $build($category->id),
                ];
            }, $items);
        };

        return $build(0);
    }
}
