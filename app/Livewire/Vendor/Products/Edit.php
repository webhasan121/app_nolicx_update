<?php

namespace App\Livewire\Vendor\Products;

use App\HandleImageUpload;
use App\Models\Category;
use App\Models\Product;
use App\Models\product_has_attribute;
use App\Models\product_has_image;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[layout('layouts.app')]
class Edit extends Component
{

    use WithFileUploads, HandleImageUpload;

    #[URL]
    public $product, $nav = 'Product';

    public $data, $categories, $description;
    public $products, $thumb, $relatedImage = [], $newImage = [], $attr = [], $newseothumb;
    // protected $Listeners = ["$refresh"];

    protected $listeners = ['editorUpdated' => 'updateContent'];
    #[On('refresh')]
    public function mount()
    {
        $this->data();
    }

    public function data()
    {


        $this->categories = Category::getAll();

        $this->data = auth()->user()?->myProducts()->withTrashed()->find(decrypt($this->product));

        if (!$this->data) {
            // Optionally redirect or show an error
            $this->dispatch('error', 'Product not found.');
            return;
        }

        // dd($this->data);

        $this->data->load(['isResel']);
        // if ($this->data->trashed()) {
        //     $this->redirectIntended(route('vendor.products.view'), true);
        // }


        $this->products = $this->data->toArray();
        // dd(Product::find($this->data));
        // dd(Product::query()->whereIn('id', $this->data->resel->pluck('product_id'))->get());
        $this->relatedImage = $this->data->showcase?->toArray() ?? [];
        $this->attr = $this->data->attr?->toArray() ?? [];

        $this->description = $this->products['description'];
    }

    public function updateContent($html)
    {
        $this->description = $html;
    }

    public function save()
    {
        $this->validate(
            [
                'products.name' => ['required'],
                'products.title' => ['required'],
                'products.category_id' => ['required'],
                'products.buying_price' => ['required'],
                'products.price' => ['required'],
                'products.unit' => ['required'],
                'products.thumbnail' => ['required'],
            ]
        );

        // dd($this->products);
        $this->data->name = $this->products['name'];
        $this->data->title = $this->products['title'];
        $this->data->category_id = $this->products['category_id'];
        $this->data->buying_price = $this->products['buying_price'];
        $this->data->price = $this->products['price'];
        $this->data->discount = $this->products['discount'];
        $this->data->offer_type = $this->products['offer_type'];
        $this->data->display_at_home = $this->products['display_at_home'];
        $this->data->unit = $this->products['unit'];
        $this->data->description = $this->description;
        $this->data->thumbnail = $this->handleImageUpload($this->thumb, 'products', $this->products['thumbnail']);

        $this->data->meta_title = $this->products['meta_title'];
        $this->data->meta_description = $this->products['meta_description'];
        $this->data->keyword = $this->products['keyword'];
        $this->data->meta_tags = $this->products['meta_tags'];
        if ($this->newseothumb) {
            $this->data->meta_thumbnail = $this->handleImageUpload($this->newseothumb, 'products-seo', $this->products['meta_thumbnail']);
        }

        $this->data->cod = $this->products['cod'];
        $this->data->courier = $this->products['courier'];
        $this->data->hand = $this->products['hand'];
        $this->data->shipping_in_dhaka = $this->products['shipping_in_dhaka'];
        $this->data->shipping_out_dhaka = $this->products['shipping_out_dhaka'];
        $this->data->shipping_note = $this->products['shipping_note'];

        $this->data->save();

        if ($this->attr && $this->attr['id']) {
            $this->data->attr->update(
                [
                    'name' => $this->attr['name'],
                    'value' => $this->attr['value'],
                ]
            );
        } else {
            product_has_attribute::create(
                [
                    'product_id' => decrypt($this->product),
                    'name' => $this->attr['name'],
                    'value' => $this->attr['value'],
                ]
            );
        }

        if ($this->newImage) {
            foreach ($this->newImage as $key => $image) {
                product_has_image::create([
                    'product_id' => decrypt($this->product),
                    'image' => $this->handleImageUpload($image, 'product-showcase', $this->newImage[$key]),
                ]);
            }
        }
        $this->reset('newImage');
        $this->dispatch('refresh');
        $this->dispatch('success', 'Product Updated !');
    }

    public function restoreFromTrash()
    {
        // if ($this->data->resel) {
        //     $rp = Product::whereIn('id', $this->data->resel?->pluck('product_id'))->get();
        //     dd($rp);
        // }

        $this->data->restore();
        $this->dispatch('success', "Restore From Trash");
        $this->dispatch('refresh');
    }

    public function moveToTrash()
    {
        $this->data->delete();
        $this->dispatch('success', "Product moved to trashed");
        $this->dispatch('refresh');
    }

    public function erageOldImage($id)
    {
        $img = $this->data->showcase->find($id);
        // dd($img);
        if ($img) {
            // unlink(asset('storage/' . $img));
            // Storage::disk('public')->delete($oldImage);
            $img->delete();
            FacadesStorage::disk('public')->delete($img->image);
        }

        // $this->dispatch('refresh');
        $this->data();
        $this->dispatch('success', 'Image Deletd !');
    }


    public function render()
    {
        return view('livewire.vendor.products.edit');
    }
}
