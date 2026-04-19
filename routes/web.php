<?php

use App\Events\ProductComissions;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryProductsController;
use App\Http\Controllers\User\ProfileEditController;
use App\Http\Controllers\ProductDetailsController;
use App\Http\Controllers\ProductsIndexController;
use App\Http\Controllers\ProductOrderController;
use App\Http\Controllers\ProductComissionController;
use App\Http\Controllers\CategoryIndexController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ShopsController;
use App\Http\Controllers\WebPageController;
use App\Models\Category;
use App\Models\country;
use App\Models\DistributeComissions;
use App\Models\Order;
use App\Models\Product;
use App\Models\Static_slider;
use App\Models\TakeComissions;
use App\Models\User;
use App\Support\ResellerDashboardOverview;
use App\Support\RiderConsignmentIndexData;
use App\Support\SystemDashboardOverview;
use App\Support\VendorDashboardOverview;
use App\Support\VendorOrdersIndexData;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;


Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::middleware('auth')->post('/cart/add', [CartController::class, 'store']);

Route::get('dashboard', function () {
    if (auth()->user()->hasAnyRole(['system', 'admin']) || auth()->user()->can('access_vendor_dashboard') || auth()->user()->can('access_reseller_dashboard') || auth()->user()->can('access_rider_dashboard')) {
        return Inertia::render('Dashboard', [
            'systemOverview' => SystemDashboardOverview::get(),
            'resellerOverview' => ResellerDashboardOverview::get(),
            'riderConsignmentIndex' => RiderConsignmentIndexData::get(auth()->user(), request()->only([
                'status',
                'created_at',
                'start_time',
                'end_time',
            ])),
            'vendorOverview' => VendorDashboardOverview::get(auth()->user()),
            'vendorOrdersIndex' => VendorOrdersIndexData::get(auth()->user(), request()->only([
                'nav',
                'delivery',
                'create',
                'start_date',
                'end_date',
                'area',
                'find',
                'page',
            ])),
        ]);
    } else {
        return redirect()->route('user.dash');
    }
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware(['auth'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileEditController::class, 'edit'])->name('profile');
});

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

Route::middleware('auth')->prefix('/u/')->group(function () {
    Route::get('/profile', [ProfileEditController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileEditController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('products', [ProductsIndexController::class, 'index'])->name('products.index');
Route::get('category/{cat}/products', [CategoryProductsController::class, 'index'])->name('category.products');

Route::get('category', [CategoryIndexController::class, 'index'])->name('category.index');

Route::get('product/{id}/{slug}', [ProductDetailsController::class, 'show'])->name('products.details')->middleware('products.view.add');
Route::middleware('auth')->post('product/{id}/{slug}/task', [ProductDetailsController::class, 'countTask'])->name('products.details.task');

Route::get('product/order/{id}/{slug}', [ProductOrderController::class, 'create'])->name('product.makeOrder')->middleware('auth');
Route::post('product/order/{id}/{slug}', [ProductOrderController::class, 'store'])->name('product.makeOrder.store')->middleware('auth');
Route::get('product/order/location/cities/{state}', [ProductOrderController::class, 'cities'])->name('product.makeOrder.cities')->middleware('auth');


/**shops */
Route::get('/shops', [ShopsController::class, 'index'])->name('shops.reseller');
Route::get('/shops/{id}/{name}', [ShopsController::class, 'show'])->name('shops.visit');


/** search */
Route::get('/search', [SearchController::class, 'index'])->name('search');


// other page route for user
Route::get('about-us', function () {
    return view('user.pages.about');
})->name('about.us');

Route::get('about-policy', function () {
    return view('user.privacy.policies');
})->name('about.policy');


Route::get('earning', function () {
    return view('user.pages.earn');
})->name('about.earn');

Route::get('terms', function () {
    return view('user.pages.terms');
})->name('about.terms');

Route::get('return', function () {
    return view('user.pages.return_refund');
})->name('about.return');

Route::get('contact', function () {
    return view('contact');
})->name('about.contact');

require __DIR__ . '/auth.php';


Route::get('/test', function () {
    dd(config('app.system_email'));
});


Route::get('/user-agents', function (Request $request) {
    return config('app.system_email');
    try {
        // return ProductComissions::dispatch(5);

        $pcc = new ProductComissionController();
        // $pcc->roleBackDistributedComissions(Order::query()->first()->id);
        return $pcc->dispatchProductComissionsListeners(27);
        // $pcc->confirmTakeComissions(Order::query()->first()->id);
        // return 'success';
        // return DistributeComissions::query()
        //     ->where('order_id', 1)
        //     ->pending()
        //     ->groupBy('user_id')
        //     ->select('user_id', DB::raw('SUM(amount) as total_amount'))
        //     ->get();
    } catch (\Throwable $th) {
        throw $th;
    }
});

Route::get('page/{slug}', [WebPageController::class, 'show'])->name('web.pages');

Route::get('/queue', function () {
    Artisan::call('queue:work');
    return redirect()->back();
})->name('queue');

// Route::get('check-product', function () {
//     // get the product, those who have the isResel relation
//     return Product::query()->whereHas('isResel', function ($query) {
//         $query->with('isResel');
//     })->with('isResel')->get();
// });

Route::get('/countries', function () {
    $countries = country::get();
    return response()->json($countries);
})->name('countries');

Route::prefix('api')->controller(LocationController::class)->name('location.')->group(function () {
    Route::get('/countries', 'countries')->name('countries');
});
