<?php

namespace App\Livewire\Pages\Shops;

use App\Models\reseller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Livewire\Component;
use Illuminate\Support\Str;

class ShopList extends Component
{
    public function render()
    {
        $shops = [];
        $q = reseller::query();
        if (Auth::check()) {
            $shops = $q->where(['country' => auth()->user()?->country, 'status' => 'Active'])->paginate(config('app.paginate'));
        } else {
            $shops = $q->get();
        }
        return view('livewire.pages.shops.shop-list', compact('shops'));
    }
}
