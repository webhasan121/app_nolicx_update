<?php

namespace App\Livewire\Vendor\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\HandleImageUpload;
use App\Models\Category;
use App\Models\Product;
use App\Models\product_has_attribute;
use App\Models\product_has_image;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

#[layout('layouts.app')]
class Create extends Component
{
    use HandleImageUpload, WithFileUploads;

    #[Validate]
    public $products = [], $thumb, $relatedImage = [], $newImage = [], $categories, $attrName, $attrValue;
    public $belongs_to, $shop, $ableToCreate = true, $meta = ['meta_thumbnail' => '', 'meta_title' => '', 'keyword' => '', 'meta_tags' => '', 'meta_description' => ''];

    protected function rules()
    {
        return [
            'products.name' => 'required|min:5',
            'products.title' => 'required|min:5|max:255',
            'products.category_id' => 'required',
            'products.buying_price' => 'required',
            'products.price' => 'required',
            'thumb' => 'required',
            'newImage.*' => 'image|max:2048',
        ];
    }


    #[On('refresh')]
    public function mount()
    {

        /**
         * if any category not fount
         * throw an warning,
         * as every product must belongs to a category
         *
         * */

        $this->products['description'] = 'This is description';
        $roles = auth()->user()->getRoleNames();
        // dd($roles);
        if (count($roles) > 2) {
            $this->belongs_to = auth()->user()->active_nav;
        } else {
            $this->belongs_to = auth()->user()->isVendor() ? 'vendor' : 'reseller';
        }
        // dd($this->account);

        if ($this->belongs_to == 'reseller') {
            $this->shop = auth()->user()->resellerShop();
        }

        if ($this->belongs_to == 'vendor') {
            $this->shop = auth()->user()->vendorShop();
        };

        // dd($this->shop);

        if (!$this->shop) {
            $this->dispatch('error', 'You must have a shop to create a product');
            return redirect()->route('vendor.shops.create');
        }

        // if max products reached
        if (auth()->user()->myProducts()->count() >= $this->shop->max_product_upload) {
            $this->dispatch('error', 'You have reached the maximum number of products allowed for your shop.');
            $this->ableToCreate = false;
        }

        $this->categories = Category::getAll();
    }

    public function removeImage($index)
    {
        unset($this->newImage[$index]);
        $this->newImage = array_values($this->newImage); // reindex array
    }


    public function create()
    {

        if (auth()->user()->myProducts()->count() >= $this->shop->max_product_upload) {
            $this->dispatch('error', 'You have reached the maximum number of products allowed for your shop.');
            $this->ableToCreate = false;
            return;
        }
        $this->validate();
        $data = array_merge(
            $this->products,
            [
                'slug' => Str::slug($this->products['title']),
                'thumbnail' => $this->handleImageUpload($this->thumb, 'products', null),
                'belongs_to_type' => $this->belongs_to,
                'country' => Auth::user()->country ?? 'Bangladesh',
                'state' => Auth::user()->state ?? null,

                'meta_title' => $this->meta['meta_title'],
                'meta_description' => $this->meta['meta_description'],
                'meta_tags' => $this->meta['meta_tags'],
                'keyword' => $this->meta['keyword'],
                'meta_thumbnail' => $this->handleImageUpload($this->meta['meta_thumbnail'], 'products-seo', ''),
                'cod' => $this->products['cod'],
                'courier' => $this->products['courier'],
                'hand' => $this->products['hand'],
            ]
        );

        try {
            $pd = Product::create($data);
            if ($pd->id && $this->attrName) {

                // update product attr
                product_has_attribute::create(
                    [
                        'product_id' => $pd->id,
                        'name' => $this->attrName ?? "",
                        'value' => $this->attrValue ?? "",
                    ]
                );
            }

            if ($pd->id && $this->newImage) {
                foreach ($this->newImage as $image) {
                    product_has_image::create([
                        'product_id' => $pd->id,
                        'image' => $this->handleImageUpload($image, 'product-showcase', null),
                    ]);
                }
                $this->newImage = []; // reset after saving
            }

            $this->dispatch('success', 'Product Created');
            $this->redirectIntended(route('vendor.products.edit', ['product' => encrypt($pd->id)]), true);
        } catch (\Throwable $th) {
            $this->dispatch('error', 'Have an error to upload product.');
        }
    }

    public function render()
    {
        return view('livewire.vendor.products.create');
    }
}
