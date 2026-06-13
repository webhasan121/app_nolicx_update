<?php

namespace App\Http\Controllers;

use App\Models\reseller;
use App\Models\Category;
use App\Models\city as CityModel;
use App\Models\country as CountryModel;
use App\Models\Product;
use App\Models\Slider as SliderModel;
use App\Models\Slider_has_slide;
use App\Models\state as StateModel;
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
        $userLocation = $this->currentUserLocation();

        if ($state === 'me') {
            $location = $userLocation;
        }

        $query = reseller::where('status', 'Active');

        if ($q !== '') {
            $keyword = mb_strtolower($q);

            $query->where(function ($builder) use ($keyword) {
                $builder->whereRaw('LOWER(shop_name_en) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(shop_name_bn) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(district) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(upozila) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(village) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(country) LIKE ?', ["%{$keyword}%"]);
            });
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
            ? $query->latest('id')->paginate(20)->withQueryString()
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
            'userLocation' => $userLocation,
            'showFiltered' => $q !== '' || $location !== '',
        ]);
    }

    private function currentUserLocation(): string
    {
        $user = Auth::user();

        if (!$user) {
            return '';
        }

        foreach ([
            ['value' => $user->city, 'model' => CityModel::class],
            ['value' => $user->state, 'model' => StateModel::class],
            ['value' => $user->country, 'model' => CountryModel::class],
        ] as $candidate) {
            $location = $this->locationName($candidate['value'], $candidate['model']);

            if ($location !== '') {
                return $location;
            }
        }

        return '';
    }

    private function locationName($value, string $model): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (ctype_digit($value)) {
            return trim((string) $model::query()->find((int) $value)?->name);
        }

        return $value;
    }

    public function show($id, $name): Response
    {
        $shop = reseller::query()
            ->with('user')
            ->findOrFail($id);

        $products = Product::query()
            ->active()
            ->reseller()
            ->where('user_id', $shop?->user?->id)
            ->get([
                'id',
                'name',
                'title',
                'slug',
                'thumbnail',
                'offer_type',
                'discount',
                'price',
                'unit',
            ]);


        return Inertia::render('Shops/Show', [
            'shop' => $shop,
            'products' => $products,
        ]);
    }

    private function defaultShops()
    {
        return reseller::query()
            ->where('status', 'Active')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();
    }
}
