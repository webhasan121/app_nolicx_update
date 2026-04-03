<?php

use App\Http\Controllers\System\VendorController;
use App\Http\Middleware\AbleTo;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsActiveVendor;


use App\Livewire\Vendor\Products\Index as vendorProductsIndexPage;
use App\Livewire\Vendor\Products\Create as vendorProductsCreatePage;
use App\Livewire\Vendor\Products\Edit as vendorProductsEditPage;
use App\Livewire\Vendor\Products\Pages\Orders as vendorProductsOrderPage;
use App\Livewire\Vendor\Products\Pages\Resell as vendorProductsResellPage;


use App\Livewire\Vendor\Categories\Index as vendorCategoryIndexpage;
use App\Livewire\Vendor\Categories\Create as vendorCategoryCreatePage;
use App\Livewire\Vendor\Categories\Edit as vendorCategoryEditPage;


use App\Livewire\Vendor\Orders\Index as vendorOrderIndexPage;
use App\Livewire\Vendor\Orders\View as vendorOrderViewPage;
use App\Livewire\Vendor\Orders\Vprint as vendorOrderVPrintPage;
use App\Livewire\Vendor\Orders\Cprint as vendorOrderCPrintPage;




// Route::get('/','VendorController@index')->name('vendor.index');

Route::get('products/view', vendorProductsIndexPage::class)->name("vendor.products.view")->middleware(AbleTo::class . ":product_view");
Route::get('products', vendorProductsEditPage::class)->name("vendor.products.edit")->middleware(AbleTo::class . ":product_edit");
Route::get('products/create', vendorProductsCreatePage::class)->name("vendor.products.create")->middleware(AbleTo::class . ":product_add");
Route::get('/products/orders', vendorProductsOrderPage::class)->name('vendor.products.orders');
// Route::get('/products/orders/{order}/view', vendorProductsOrderPage::class)->name('vendor.products.orders');
Route::get('/products/resell', vendorProductsResellPage::class)->name('vendor.products.resell');
// Route::get('/products/resell/{product}/veiw', vendorProductsResellPage::class)->name('vendor.products.resell');


Route::prefix('/order')->group(function () {
    Route::get('/', vendorOrderIndexPage::class)->name('vendor.orders.index');
    Route::get('/view/{order}/', vendorOrderViewPage::class)->name('vendor.orders.view');
    Route::get('/print/client/{order}', vendorOrderCPrintPage::class)->name('vendor.orders.cprint');
    Route::get('/print/{order}', vendorOrderVPrintPage::class)->name('vendor.orders.print');
});


Route::prefix('category')->group(function () {
    Route::get('/', vendorCategoryIndexpage::class)->name('vendor.category.view')->middleware(AbleTo::class . ":category_view");
    Route::get('/create', vendorCategoryCreatePage::class)->name('vendor.category.create')->middleware(AbleTo::class . ":category_create");
    Route::get('/edit/{cat}', vendorCategoryEditPage::class)->name('vendor.category.edit')->middleware(AbleTo::class . ":category_edit");
});
