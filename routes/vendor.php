<?php

use App\Http\Controllers\System\VendorController;
use App\Http\Controllers\Vendor\CategoryController;
use App\Http\Controllers\Vendor\OrdersController;
use App\Http\Controllers\Vendor\ProductsController;
use App\Http\Middleware\AbleTo;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsActiveVendor;


// Route::get('/','VendorController@index')->name('vendor.index');

Route::get('products/view', [ProductsController::class, 'index'])->name("vendor.products.view")->middleware(AbleTo::class . ":product_view");
Route::get('products/view/print', [ProductsController::class, 'print'])->name("vendor.products.print")->middleware(AbleTo::class . ":product_view");
Route::post('products/view/trash', [ProductsController::class, 'bulkTrash'])->name("vendor.products.bulk-trash")->middleware(AbleTo::class . ":product_view");
Route::post('products/view/restore', [ProductsController::class, 'bulkRestore'])->name("vendor.products.bulk-restore")->middleware(AbleTo::class . ":product_view");
Route::get('products/create', [ProductsController::class, 'create'])->name("vendor.products.create")->middleware(AbleTo::class . ":product_add");
Route::post('products/create', [ProductsController::class, 'store'])->name("vendor.products.store")->middleware(AbleTo::class . ":product_add");
// **Route::get('/products/orders', vendorProductsOrderPage::class)->name('vendor.products.orders');
// Route::get('/products/orders/{order}/view', vendorProductsOrderPage::class)->name('vendor.products.orders');
Route::get('/products/resell', [ProductsController::class, 'resell'])->name('vendor.products.resell');
// Route::get('/products/resell/{product}/veiw', vendorProductsResellPage::class)->name('vendor.products.resell');
Route::get('products/{product}', [ProductsController::class, 'edit'])->name("vendor.products.edit")->middleware(AbleTo::class . ":product_edit");
Route::post('products/{product}/update', [ProductsController::class, 'update'])->name("vendor.products.update")->middleware(AbleTo::class . ":product_edit");
Route::post('products/{product}/trash', [ProductsController::class, 'trash'])->name("vendor.products.trash")->middleware(AbleTo::class . ":product_edit");
Route::post('products/{product}/restore', [ProductsController::class, 'restore'])->name("vendor.products.restore")->middleware(AbleTo::class . ":product_edit");
Route::delete('products/{product}/images/{image}', [ProductsController::class, 'destroyImage'])->name("vendor.products.images.destroy")->middleware(AbleTo::class . ":product_edit");


Route::prefix('/order')->group(function () {
    Route::get('/', [OrdersController::class, 'index'])->name('vendor.orders.index');
    Route::get('/view/{order}/', [OrdersController::class, 'view'])->name('vendor.orders.view');
    Route::post('/view/{order}/status', [OrdersController::class, 'updateStatus'])->name('vendor.orders.status');
    Route::post('/view/{order}/rider', [OrdersController::class, 'assignRider'])->name('vendor.orders.rider.assign');
    Route::delete('/view/{order}/rider/{cod}', [OrdersController::class, 'removeRider'])->name('vendor.orders.rider.remove');
    Route::get('/print/client/{order}', [OrdersController::class, 'cprint'])->name('vendor.orders.cprint');
    Route::get('/print/{order}', [OrdersController::class, 'vprint'])->name('vendor.orders.print');
    Route::get('/print-summary', [OrdersController::class, 'summaryPrint'])->name('vendor.orders.summary.print');
});


Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('vendor.category.view')->middleware(AbleTo::class . ":category_view");
    Route::post('/', [CategoryController::class, 'store'])->name('vendor.category.store')->middleware(AbleTo::class . ":category_create");
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('vendor.category.destroy')->middleware(AbleTo::class . ":category_edit");
    Route::get('/create', [CategoryController::class, 'create'])->name('vendor.category.create')->middleware(AbleTo::class . ":category_create");
    Route::get('/edit/{cat}', [CategoryController::class, 'edit'])->name('vendor.category.edit')->middleware(AbleTo::class . ":category_edit");
    Route::post('/edit/{cat}', [CategoryController::class, 'update'])->name('vendor.category.update')->middleware(AbleTo::class . ":category_edit");
});
