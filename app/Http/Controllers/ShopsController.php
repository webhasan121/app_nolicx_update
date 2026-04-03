<?php

namespace App\Http\Controllers;

use App\Models\reseller;
use App\Models\Slider as SliderModel;
use App\Models\Slider_has_slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ShopsController extends Controller
{
    public function index(Request $request): Response
    {
        $q = $request->string('q')->toString();
        $location = $request->string('location')->toString();
        $state = $request->string('state')->toString();

        $query = reseller::where('status', 'Active');

        if (Auth::check()) {
            $query->where('country', auth()->user()?->country);
        }

        if ($q !== '') {
            $query->whereAny(
                ['shop_name_en', 'shop_name_bn'],
                'like',
                '%' . Str::ucfirst($q ?: $location) . '%'
            );
        }

        if ($location !== '') {
            $formattedLocation = Str::ucfirst($location);

            $query->where(function ($builder) use ($formattedLocation) {
                $builder->where('district', 'like', '%' . $formattedLocation . '%')
                    ->orWhere('upozila', 'like', '%' . $formattedLocation . '%')
                    ->orWhere('village', 'like', '%' . $formattedLocation . '%')
                    ->orWhere('country', 'like', '%' . $formattedLocation . '%');
            });
        }

        $shops = ($q !== '' || $location !== '')
            ? $query->paginate(config('app.paginate'))->withQueryString()
            : $this->defaultShops();

        $sliderIds = SliderModel::query()
            ->where('status', true)
            ->whereNot('placement', 'apps')
            ->orderByDesc('id')
            ->pluck('id');

        $slides = Slider_has_slide::query()
            ->whereIn('slider_id', $sliderIds)
            ->get();

        return Inertia::render('Shops/Index', [
            'slides' => $slides,
            'shops' => $shops,
            'filters' => [
                'q' => $q,
                'location' => $location,
                'state' => $state,
            ],
            'showFiltered' => $q !== '' || $location !== '',
        ]);
    }

    private function defaultShops()
    {
        $query = reseller::query();

        if (Auth::check()) {
            return $query
                ->where([
                    'country' => auth()->user()?->country,
                    'status' => 'Active',
                ])
                ->paginate(config('app.paginate'))
                ->withQueryString();
        }

        return $query->paginate(config('app.paginate'))->withQueryString();
    }
}
