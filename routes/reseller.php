<?php

use App\Http\Controllers\ResellerController;
use App\Http\Middleware\AbleTo;
use App\Livewire\EarnBySell\Index as EarnBySellIndex;
use Illuminate\Support\Facades\Route;


use App\Livewire\Shops\Shop;
use App\Livewire\Reseller\Products\Index as productIndexPage;
use App\Livewire\Reseller\Products\Create as productCreatePage;
use App\Livewire\Reseller\Products\Edit as productEditPge;

use App\Livewire\Reseller\Categories\Index as categoryIndexPage;

use App\Livewire\Reseller\Resel\Products\Index as reselProductsIndexPage;
use App\Livewire\Reseller\Resel\Products\View as reselProductViewPage;
use App\Livewire\Reseller\Resel\Categories as reselCategoriesViewPage;
use App\Livewire\Reseller\Resel\Orders\Index as reselOrderIndexPage;

use App\Livewire\Reseller\Orders\Index as resellerOrderIndex;
use App\Livewire\Reseller\Orders\View;
use App\Livewire\Reseller\Resel\Shops;
use App\Livewire\Vendor\Comissions\Index;

Route::prefix('/r/')->group(function () {
    // routes for products
    Route::get('products/list', productIndexPage::class)->name("reseller.products.list")->middleware(AbleTo::class . ":product_view");
    Route::get('products/create', productCreatePage::class)->name('reseller.products.create')->middleware(AbleTo::class . ":product_add");
    Route::get('products/{id}', productEditPge::class)->name('reseller.products.edit');


    Route::get('/categories', categoryIndexPage::class)->name('reseller.categories.list')->middleware(AbleTo::class . ":category_view");

    // route for categories

    Route::prefix('orders')->name('reseller.order.')->group(function () {
        Route::get('/', resellerOrderIndex::class)->name('index');
        Route::get('/{order}', View::class)->name('view');
    });


    // resel product view 
    Route::get('/resel', reselProductsIndexPage::class)->name('reseller.resel-product.index');
    Route::get('/resel/product/{pd}', reselProductViewPage::class)->name('reseller.resel-product.veiw');
    Route::get('/resel/categories', reselCategoriesViewPage::class)->name('reseller.resel-products.catgory');
    Route::get('/order/resel', reselOrderIndexPage::class)->name('reseller.resel-order.index');

    // comissions
    Route::get('/comissions', Index::class)->name('reseller.comissions.index');

    Route::get('/{user}/shop', Shop::class)->name('my-shop');

    Route::get('/product/{id}/order', [ResellerController::class, 'productOrder'])->name('reseller.product.order');

    // sell
    Route::get('/sels', EarnBySellIndex::class)->name('reseller.sel.index');

    // vendor shop for reseller 
    Route::get('/shops', Shops::class)->name('shops');
})->middleware(AbleTo::class . ":access_reseller_dashboard");
