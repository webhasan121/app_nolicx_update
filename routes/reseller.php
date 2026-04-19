<?php

use App\Http\Controllers\ResellerController;
use App\Http\Controllers\Reseller\ReselProductsController;
use App\Http\Controllers\Reseller\ReselShopsController;
use App\Http\Controllers\Reseller\CategoriesController;
use App\Http\Controllers\Reseller\EarnBySellController;
use App\Http\Controllers\Reseller\OrdersController;
use App\Http\Controllers\Reseller\ProductsController;
use App\Http\Controllers\Reseller\ComissionsController as ResellerComissionsController;
use App\Http\Controllers\Reseller\ReselOrdersController;
use App\Http\Controllers\ResellerShopController;
use App\Http\Middleware\AbleTo;
use Illuminate\Support\Facades\Route;


Route::prefix('/r/')->group(function () {
    // routes for products
    Route::get('products/list', [ProductsController::class, 'index'])->name("reseller.products.list")->middleware(AbleTo::class . ":product_view");
    Route::get('products/print', [ProductsController::class, 'print'])->name("reseller.products.print")->middleware(AbleTo::class . ":product_view");
    // Route::get('products/create', productCreatePage::class)->name('reseller.products.create')->middleware(AbleTo::class . ":product_add");
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


    Route::get('/categories', [CategoriesController::class, 'index'])->name('reseller.categories.list')->middleware(AbleTo::class . ":category_view");
    Route::post('/categories', [CategoriesController::class, 'store'])->name('reseller.categories.store');

    // route for categories

    Route::prefix('orders')->name('reseller.order.')->group(function () {
        Route::get('/', [OrdersController::class, 'index'])->name('index');
        Route::get('/{order}', [OrdersController::class, 'view'])->name('view');
        Route::post('/{order}/status', [OrdersController::class, 'updateStatus'])->name('status');
    });


    // resel product view
    Route::get('/resel', [ReselProductsController::class, 'index'])->name('reseller.resel-product.index');
    Route::post('/resel/order/{product}', [ReselProductsController::class, 'order'])
        ->name('reseller.resel-product.order');
    Route::get('/resel/product/{pd}', [ReselProductsController::class, 'show'])
        ->name('reseller.resel-product.veiw');
    Route::post('/resel/product/{product}/clone', [ReselProductsController::class, 'clone'])
        ->name('reseller.resel-product.clone');
    Route::get('/resel/categories', [ReselProductsController::class, 'categories'])->name('reseller.resel-products.catgory');
    Route::get('/order/resel', [ReselOrdersController::class, 'index'])->name('reseller.resel-order.index');

    // comissions
    Route::get('/comissions', [ResellerComissionsController::class, 'index'])->name('reseller.comissions.index');

    Route::get('/{user}/shop', [ResellerShopController::class, 'show'])->name('my-shop');
    Route::post('/{user}/shop/update', [ResellerShopController::class, 'update'])->name('my-shop.update');

    Route::get('/product/{id}/order', [ResellerController::class, 'productOrder'])->name('reseller.product.order');

    // sell
    Route::get('/sels', [EarnBySellController::class, 'index'])->name('reseller.sel.index');
    Route::get('/sels/print', [EarnBySellController::class, 'print'])->name('reseller.sel.print');

    // vendor shop for reseller
    Route::get('/shops', [ReselShopsController::class, 'index'])->name('shops');
    Route::get('/shops/print', [ReselShopsController::class, 'print'])->name('shops.print');
})->middleware(AbleTo::class . ":access_reseller_dashboard");
