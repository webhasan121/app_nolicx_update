<?php

namespace App\Livewire\Shops;

use App\HandleImageUpload;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[layout('layouts.app')]
class Shop extends Component
{
    use WithFileUploads, HandleImageUpload;

    public $account, $shop, $shopArray = [], $newLogo, $newBanner;

    public function mount()
    {
        $this->account = auth()->user()->active_nav;
        $this->getData();
    }

    public function getData()
    {
        if ($this->account == 'reseller') {
            $this->shopArray = auth()->user()->resellerShop()->toArray();
        }

        if ($this->account == 'vendor') {
            $this->shopArray  = auth()->user()->vendorShop()->toArray();
        }
        // dd($this->shop);
    }

    public function updateInfo()
    {
        if ($this->newLogo) {
            $this->shopArray['logo'] = $this->handleImageUpload($this->newLogo, 'shop-logo', $this->shopArray['logo']);;
        }
        if ($this->newBanner) {
            $this->shopArray['banner'] = $this->handleImageUpload($this->newBanner, 'shop-banner', $this->shopArray['banner']);;
        }

        auth()->user()->resellerShop()->update($this->shopArray);
        $this->dispatch('success', 'updated');
    }

    public function render()
    {
        return view('livewire.shops.shop');
    }
}
