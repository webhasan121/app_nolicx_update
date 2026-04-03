<?php

namespace App\Livewire\Reseller\Resel\Products;

use App\Http\Controllers\ResellerController;
use App\Models\Category;
use App\Models\Product;
use App\Models\product_has_image;
use App\Models\Reseller_resel_product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class View extends Component
{
    #[URL]
    public $pd;
    public $products, $confirmResel = false, $confirmOrder, $forResel = [], $reselPrice, $reselDiscountPrice, $isReselWithDiscountPrice = false, $resellerCat, $categories, $ableToAdd = true, $totalReselProducts = 0;

    public function mount()
    {
        $this->products = Product::where(['belongs_to_type' => 'vendor', 'id' => $this->pd, 'status' => 'Active'])->first();
        $this->forResel = $this->products->only('name', 'title', 'slug', 'discount', 'description', 'thumbnail', 'price', 'meta_title', 'meta_description', 'meta_tags', 'keyword', 'meta_thunbnail');

        if (empty($this->forResel['offer_type'])) {

            $this->reselPrice = $this->forResel['discount'] + 150;
            $this->reselDiscountPrice = $this->forResel['discount'] + 100;
        } else {
            $this->reselPrice = $this->forResel['price'] + 150;
            $this->reselDiscountPrice = $this->forResel['price'] + 100;
        }

        // get all the categories those are not have any parent
        $this->categories = Category::getAll();
        if (!$this->products) {
            return redirect()->back();
        }
        $this->totalReselProducts = Reseller_resel_product::where(['user_id' => auth()->user()->id])->count();
        if (auth()->user()->resellerShop()->allow_max_resell_product) {
            $this->ableToAdd = $this->totalReselProducts < auth()->user()->resellerShop()->max_resell_product ? true : false;
        } else {
            $this->ableToAdd = false;
        }
    }

    public function confirmClone()
    {
        $this->isReselWithDiscountPrice = $this->reselDiscountPrice ? true : false;
        $this->reselDiscountPrice = $this->isReselWithDiscountPrice ? $this->reselDiscountPrice : null;
        // clone product basic info
        // dd('ok');
        $isAlreadycloned = Reseller_resel_product::where(['parent_id' => $this->products->id, 'user_id' => auth()->user()->id])->exists();
        if (!$isAlreadycloned) {
            $this->validate(
                [
                    'reselPrice' => ['required'],
                    'resellerCat' => ['required']
                ]
            );
            if (!empty($this->resellerCat || !empty($this->reselPrice))) {
                // $rc = new ResellerController();
                // $rc->cloneProducts($this->products->id, $this->reselPrice, $this->resellerCat);

                $this->forResel['user_id'] = auth()->user()->id;
                $this->forResel['belongs_to_type'] = 'reseller';
                $this->forResel['buying_price'] = $this->products->totalPrice();
                $this->forResel['unit'] = $this->products?->unit ?? 1;
                $this->forResel['offer_type'] = $this->isReselWithDiscountPrice;
                $this->forResel['discount'] = $this->isReselWithDiscountPrice ? $this->reselDiscountPrice : null;
                $this->forResel['price'] = $this->reselPrice;
                $this->forResel['category_id'] = $this->resellerCat;
                $this->forResel['status'] = 'Active';
                $this->forResel['country'] = auth()->user()->country ?? 'Bangladesh';

                // save as new to reseller
                $newProduct = Product::create($this->forResel);

                // create link to track reseller, vendor and product
                $rrp = new Reseller_resel_product();
                $rrp->forceFill(
                    [
                        'user_id' => Auth::id(),
                        'belongs_to' => $this->products->user_id,
                        'product_id' => $newProduct->id,
                        'parent_id' => $this->products->id
                    ]
                );
                $rrp->save();

                if ($rrp) {
                    foreach ($this->products->showcase as $value) {
                        product_has_image::created(
                            [
                                'product_id' => $newProduct->id,
                                'image' => $value->image,
                            ]
                        );
                    }
                }

                // Reseller_resel_product::forcefill()


                // $this->redirectIntended('reseller.products.edit', true);
                $this->dispatch('close-modal', 'confirm-resel');
                $this->dispatch('success', 'Successfully cloned! Product added to your list');
            } else {
                $this->dispatch('error', 'Price and Category must be defined !');
            }
        } else {
            $this->dispatch('error', 'Already Cloned !');
        }
    }


    public function render()
    {
        return view('livewire.reseller.resel.products.view');
    }
}
