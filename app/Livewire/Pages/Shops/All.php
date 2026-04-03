<?php

namespace App\Livewire\Pages\Shops;

use App\Models\reseller;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Str;

#[layout('layouts.user.app')]
class All extends Component
{
    use WithPagination;
    #[URL]
    public $q, $location;
    public $state = '';

    public function getShopByMyLocation()
    {
        if (auth()->user()->city) {
            $this->location = auth()->user()->city;
            $this->state = 'me';
            $this->q = '';
        } else {
            $this->dispatch('warning', 'You do not have location specified.');
        }
        // dd(auth()->user()->city ?? 'Bangladesh');
    }

    public function getAllShops()
    {
        $this->state = 'all';
        $this->q = '';
        $this->location = 'Bangladesh';
    }

    public function render()
    {

        $query = reseller::where('status', 'Active');

        if (Auth::check()) {
            $query->where(['country' => auth()->user()?->country]);
        }

        if ($this->q) {
            $query->whereAny(['shop_name_en', 'shop_name_bn'], 'like', "%" . Str::ucfirst($this->q ?? $this->location) . "%");
        }

        if ($this->location) {
            $query->where(function ($q) {
                $q->where('district', 'like', '%' . Str::ucfirst($this->location) . '%')
                    ->orWhere('upozila', 'like', '%' . Str::ucfirst($this->location) . '%')
                    ->orWhere('village', 'like', '%' . Str::ucfirst($this->location) . '%')
                    ->orWhere('country', 'like', '%' . Str::ucfirst($this->location) . '%');
            });
        }

        $shops = $query->paginate(config('app.paginate'));
        return view('livewire.pages.shops.all', compact('shops'));
    }
}
