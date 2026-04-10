<?php

use App\Http\Controllers\ResellerController;
use App\Http\Controllers\Reseller\ReselProductsController;
use App\Http\Controllers\Reseller\ReselShopsController;
use App\Http\Controllers\Reseller\EarnBySellController;
use App\Http\Controllers\Reseller\ProductsController;
use App\Http\Controllers\Reseller\ComissionsController as ResellerComissionsController;
use App\Http\Controllers\Reseller\ReselOrdersController;
use App\Http\Controllers\ResellerShopController;
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
    Route::get('products/list/old', productIndexPage::class)->name("reseller.products.list.old")->middleware(AbleTo::class . ":product_view");
    Route::get('products/list', [ProductsController::class, 'index'])->name("reseller.products.list")->middleware(AbleTo::class . ":product_view");
    Route::get('products/print', [ProductsController::class, 'print'])->name("reseller.products.print")->middleware(AbleTo::class . ":product_view");
    // Route::get('products/create', productCreatePage::class)->name('reseller.products.create')->middleware(AbleTo::class . ":product_add");
    Route::get('products/{id}/old', productEditPge::class)
        ->name('reseller.products.edit.old')
        ->middleware(AbleTo::class . ":product_edit");
    Route::get('products/{id}', [ProductsController::class, 'edit'])
        ->name('reseller.products.edit')
        ->middleware(AbleTo::class . ":product_edit");
    Route::post('products/{id}/update', [ProductsController::class, 'update'])
        ->name('reseller.products.update')
        ->middleware(AbleTo::class . ":product_edit");
    Route::post('products/{id}/trash', [ProductsController::class, 'trash'])
        ->name('reseller.products.trash')
        ->middleware(AbleTo::class . ":product_edit");
    Route::post('products/{id}/restore', [ProductsController::class, 'restore'])
        ->name('reseller.products.restore')
        ->middleware(AbleTo::class . ":product_edit");
    Route::delete('products/{id}/images/{image}', [ProductsController::class, 'destroyImage'])
        ->name('reseller.products.images.destroy')
        ->middleware(AbleTo::class . ":product_edit");


    Route::get('/categories', categoryIndexPage::class)->name('reseller.categories.list')->middleware(AbleTo::class . ":category_view");

    // route for categories

    Route::prefix('orders')->name('reseller.order.')->group(function () {
        Route::get('/', resellerOrderIndex::class)->name('index');
        Route::get('/{order}', View::class)->name('view');
    });


    // resel product view
    Route::get('/resel/old', reselProductsIndexPage::class)->name('reseller.resel-product.index.old');
    Route::get('/resel', [ReselProductsController::class, 'index'])->name('reseller.resel-product.index');
    Route::post('/resel/order/{product}', [ReselProductsController::class, 'order'])
        ->name('reseller.resel-product.order');
    Route::get('/resel/product/{pd}/old', reselProductViewPage::class)
        ->name('reseller.resel-product.veiw.old');
    Route::get('/resel/product/{pd}', [ReselProductsController::class, 'show'])
        ->name('reseller.resel-product.veiw');
    Route::post('/resel/product/{product}/clone', [ReselProductsController::class, 'clone'])
        ->name('reseller.resel-product.clone');
    Route::get('/resel/categories', reselCategoriesViewPage::class)->name('reseller.resel-products.catgory');
    Route::get('/order/resel/old', reselOrderIndexPage::class)->name('reseller.resel-order.index.old');
    Route::get('/order/resel', [ReselOrdersController::class, 'index'])->name('reseller.resel-order.index');

    // comissions
    Route::get('/comissions/old', Index::class)->name('reseller.comissions.index.old');
    Route::get('/comissions', [ResellerComissionsController::class, 'index'])->name('reseller.comissions.index');

    Route::get('/{user}/shop/old', Shop::class)->name('my-shop.old');
    Route::get('/{user}/shop', [ResellerShopController::class, 'show'])->name('my-shop');
    Route::post('/{user}/shop/update', [ResellerShopController::class, 'update'])->name('my-shop.update');

    Route::get('/product/{id}/order', [ResellerController::class, 'productOrder'])->name('reseller.product.order');

    // sell
    Route::get('/sels/old', EarnBySellIndex::class)->name('reseller.sel.index.old');
    Route::get('/sels', [EarnBySellController::class, 'index'])->name('reseller.sel.index');
    Route::get('/sels/print', [EarnBySellController::class, 'print'])->name('reseller.sel.print');

    // vendor shop for reseller
    Route::get('/shops/old', Shops::class)->name('shops.old');
    Route::get('/shops', [ReselShopsController::class, 'index'])->name('shops');
    Route::get('/shops/print', [ReselShopsController::class, 'print'])->name('shops.print');
})->middleware(AbleTo::class . ":access_reseller_dashboard");
