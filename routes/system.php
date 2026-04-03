<?php

use App\Http\Controllers\ProductComissionController;
use App\Http\Controllers\System\VendorController;
use App\Http\Middleware\AbleTo;
use App\Models\User;
use App\View\Components\dashboard\overview\system\VendorCount;
use App\Http\Controllers\SystemUsersController;
use App\Livewire\FooterBuilder;
use App\Livewire\Reseller\Categories\Index as CategoriesIndex;
use App\Livewire\Reseller\Orders\Index as OrdersIndex;
use App\Livewire\System\Categories\Edit as CategoriesEdit;
use App\Livewire\System\Categories\Index as SystemCategoriesIndex;
use App\Livewire\System\Comissions\Index as ComissionsIndex;
use App\Livewire\System\Comissions\Takes;
use App\Livewire\System\Comissions\TakesDetails;
use App\Livewire\System\Comissions\TakesDistributes;
use App\Livewire\System\Consignment\Index as ConsignmentIndex;
use App\Livewire\System\Deposit\Index as DepositIndex;
use App\Livewire\System\Deposit\PrintSummery;
use App\Livewire\System\EarnBySell\Index as EarnBySellIndex;
use App\Livewire\System\Geolocation\Countries;
use App\Livewire\System\Geolocation\States;
use App\Livewire\System\Geolocation\Cities;
use App\Livewire\System\Geolocation\Area;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Livewire\Volt\Volt;

use App\Livewire\System\Users\Edit as systemUserEditPage;
use App\Livewire\System\Users\Index as systemUserIndexPage;

use App\Livewire\System\Vendors\Index as vendorIndexPage;
use App\Livewire\System\Vendors\Edit as vendorEdit;
use App\Livewire\System\Vendors\Vendor\Settings as systemVendorSettingspage;
use App\Livewire\System\Vendors\Vendor\Documents as systemVendorDocumentsPage;
use App\Livewire\System\Vendors\Vendor\Products as systemVendorProductsPage;
use App\Livewire\System\Vendors\Vendor\Categories as systemVendorCategoriesPage;


use App\Livewire\System\Resellers\Index as systemResellerIndexPage;
use App\Livewire\System\Resellers\Edit as systemResellerEditPage;

use App\Livewire\System\Riders\Index as systemRiderIndexPage;
use App\Livewire\System\Riders\Edit as systemRiderEditPage;

use App\Livewire\System\Vip\Package\Index as systemVipIndexPage;
use App\Livewire\System\Vip\Users as systemVipUsersIndex;
use App\Livewire\System\Vip\Package\Create as systemVipCreatePage;
use App\Livewire\System\Vip\Package\Edit as systemVipEditPage;
use App\Livewire\System\Vip\Edit;

use App\Livewire\System\Store\Index;

use App\Livewire\System\Products\Index as systemGlobalProductsIndexPage;
use App\Livewire\System\Products\Filter;
use App\Livewire\System\Products\Edit as systemGlobalProductsEditPage;

use App\Livewire\System\Navigations\Index as NavigationsIndex;
use App\Livewire\System\Orders\Details;
use App\Livewire\System\Orders\Index as SystemOrdersIndex;
use App\Livewire\System\Orders\PrintSummery as OrdersPrintSummery;
use App\Livewire\System\Settings\Index as SettingsIndex;
use App\Livewire\System\Settings\Pages\Index as PagesIndex;
use App\Livewire\System\Settings\Pages\Edit as PagesEdit;
use App\Livewire\System\Settings\Pages\Create as PagesCreate;
use App\Livewire\System\Slider\Slider;
use App\Livewire\System\Slider\Slides;
use App\Livewire\System\Withdraw\Index as WithdrawIndex;
use App\Livewire\System\Withdraw\View as WithdrawDetails;

use App\Livewire\System\Settings\Branch\Index as BranchIndex;
use App\Livewire\System\Settings\Branch\Create as BranchCreate;
use App\Livewire\System\Settings\Branch\Modify as BranchModify;

use App\Livewire\System\Partnership\Index as PartnershipIndex;
use App\Livewire\System\Partnership\Developer as PartnershipDeveloper;
use App\Livewire\System\Partnership\Management as PartnershipManagement;

use App\Livewire\System\Report\Index as ReportIndex;
use App\Livewire\System\Report\Report;
use App\Livewire\System\Users\PrintSummery as UsersPrintSummery;
use App\Livewire\System\Vip\PrintSummery as VipPrintSummery;
use App\Livewire\System\Withdraw\Pdf;
use App\Models\DistributeComissions;
use App\Models\TakeComissions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\form;

Route::middleware(Authenticate::class)->name('system.')->prefix('system')->group(function () {

    // route for admin index to system dashboard
    Route::get('admins/old', function () {
        return view('auth.system.admins.index', ['admins' => User::role('admin')->latest('id')->get()]);
    })->name('admin.old')->middleware(AbleTo::class . ":admin_view");

    Route::get('admins', function () {
        return Inertia::render('Auth/system/admins/index', [
            'admins' => User::role('admin')->latest('id')->get()->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'permissions_count' => $admin->getPermissionNames()?->count() ?? 0,
                    'updated_at_formatted' => $admin->updated_at?->toFormattedDateString(),
                ];
            })->values()->all(),
        ]);
    })->name('admin')->middleware(AbleTo::class . ":admin_view");


    /**
     * route prefix dedicated for vendor management with permission
     */
    Route::prefix('vendors')->group(function () {
        /**
         * route for vendor index
         * as per permision
         */
        Route::get('/', vendorIndexPage::class)->name('vendor.index')->middleware(AbleTo::class . ":vendors_view");
        // Route::view('/', vendorIndexPage::class)->name('vendor.index');


        /**
         * route for vendor edit
         * @return Vendor edit page
         */
        Route::middleware(AbleTo::class . ":vendors_edit")->group(function () {
            Route::get('/{id}/edit', vendorEdit::class)->name('vendor.edit');
            Route::get('/{id}/settings', systemVendorSettingspage::class)->name('vendor.settings');
            Route::get('/{id}/documents', systemVendorDocumentsPage::class)->name('vendor.documents');
            Route::get('/{id}/products', systemVendorProductsPage::class)->name('vendor.products');
            Route::get('/{id}/categories', systemVendorCategoriesPage::class)->name('vendor.categories');
            Route::get('/{id}/orders', [VendorController::class, 'viewOrders'])->name('vendor.orders');
        });


        Route::post('/{id}/update', [VendorController::class, 'updateBySystem'])->name('vendor.update')->middleware(AbleTo::class . ":vendors_update");
    });


    /**
     * Rotue prefix dedicated for reseller management
     */
    Route::prefix('reseller')->group(function () {
        Route::get('/', systemResellerIndexPage::class)->name('reseller.index')->middleware(AbleTo::class . ":resellers_view");
        Route::get('/{id}/edit', systemResellerEditPage::class)->name('reseller.edit')->middleware(AbleTo::class . ":resellers_edit");
    });


    /**
     * route prifix dedicated for user management with permission
     */

    Route::prefix('users')->group(function () {

        // permit to make users task
        Route::get('/', systemUserIndexPage::class)->name('users.view')->middleware(AbleTo::class . ":users_view");
        Route::get('/edit/{id}/old', systemUserEditPage::class)->name('users.edit.old')->middleware(AbleTo::class . ":users_edit");
        Route::get('/edit/{id}', function ($id) {
            $user = User::findOrFail($id);

            return Inertia::render('Auth/system/users/Edit', [
                'editUser' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'coin' => $user->coin,
                    'reference' => $user->reference,
                    'reference_owner_name' => $user->getReffOwner?->owner?->name,
                    'roles' => $user->getRoleNames()->values()->all(),
                    'permissions' => $user->getPermissionNames()->values()->all(),
                    'permissions_via_role' => $user->getPermissionsViaRoles()->pluck('name')->values()->all(),
                ],
                'roles' => Role::query()->get(['id', 'name'])->toArray(),
                'permissions' => Permission::query()->get(['id', 'name'])->toArray(),
                'defaultAdminRef' => config('app.ref'),
            ]);
        })->name('users.edit')->middleware(AbleTo::class . ":users_edit");
        Route::post('/update/{id}', [SystemUsersController::class, 'admin_update'])->name("users.update")->middleware(AbleTo::class . ":users_update");
        Route::post('/{user}/roles', [SystemUsersController::class, 'update_roles'])->name('users.roles.update')->middleware(AbleTo::class . ":sync_role_to_user");
        Route::post('/{user}/permissions', [SystemUsersController::class, 'update_permissions'])->name('users.permissions.update')->middleware(AbleTo::class . ":sync_permission_to_role");
        Route::post('/recharge/{id}', function (Request $request, $id) {
            $request->validate([
                'rechargeAmount' => 'required|numeric|min:1',
            ]);

            $user = User::query()->withoutAdmin()->findOrFail($id);
            $user->increment('coin', $request->rechargeAmount);

            return redirect()->back()->with('success', 'User recharged successfully!');
        })->name('users.recharge')->middleware(AbleTo::class . ":users_update");
        Route::post('/refund/{id}', function (Request $request, $id) {
            $request->validate([
                'rechargeAmount' => 'required|numeric|min:1',
            ]);

            $user = User::query()->withoutAdmin()->findOrFail($id);
            $user->decrement('coin', $request->rechargeAmount);

            return redirect()->back()->with('success', 'User refunded successfully!');
        })->name('users.refund')->middleware(AbleTo::class . ":users_update");
        Route::get('/print-summery', UsersPrintSummery::class)->name('users.print-summery');
    });


    /**
     * routes dedicated for rider management
     *
     */
    Route::prefix('rider')->group(function () {
        Route::get('/', systemRiderIndexPage::class)->name("rider.index")->middleware(AbleTo::class . ":riders_view");
        Route::get('/{id}/edit', systemRiderEditPage::class)->name('rider.edit')->middleware(AbleTo::class . ":riders_edit");
    });


    /**
     * VIP
     */
    Route::get('/packages', systemVipIndexPage::class)->name('vip.index')->middleware(AbleTo::class . ":vip_view");
    Route::get('/package/create', systemVipCreatePage::class)->name('vip.crate')->middleware(AbleTo::class . ":vip_add");
    Route::get('/package/{packages}', systemVipEditPage::class)->name('package.edit')->middleware(AbleTo::class . ":vip_update");

    Route::get('/vip/{vip}', Edit::class)->name('vip.edit')->middleware(AbleTo::class . ":vip_user_edit");
    Route::get('/vips', systemVipUsersIndex::class)->name('vip.users')->middleware(AbleTo::class . ":vip_user_view");
    Route::get('/vips/print-summery', VipPrintSummery::class)->name('vip.print-summery')->middleware(AbleTo::class . ":vip_user_view");



    /**
     * system coin store management
     */
    Route::get('/coins', Index::class)->name('store.index')->middleware(AbleTo::class . ":store_view");


    /**
     * routes for products management for system
     */
    Route::prefix('products')->group(function () {
        Route::get('/index', systemGlobalProductsIndexPage::class)->name('products.index');
        Route::get('/{product}/edit', systemGlobalProductsEditPage::class)->name('products.edit');
    })->middleware(AbleTo::class . 'product_view');


    /**
     * Routes for manage syste categories
     */
    Route::get('/categories',  SystemCategoriesIndex::class)->name('categories.index')->middleware(AbleTo::class . ":category_view");
    Route::get('/categories/{cid}', CategoriesEdit::class)->name('categories.edit')->middleware(AbleTo::class . ":category_edit");

    /**
     * navigations
     */
    Route::get('/navigations/list', NavigationsIndex::class)->name('navigations.index');


    /**
     * slider
     */
    Route::get('/sliders', Slider::class)->name('slider.index')->middleware(AbleTo::class . ":slider_view");
    Route::get('/sliders/slides', Slides::class)->name('slider.slides')->middleware(AbleTo::class . ":slider_edit");


    /**
     * static slider
     */
    Route::get('/static-slider', \App\Livewire\System\StaticSlider\Index::class)->name('static-slider.index');
    Route::get('/static-slider/{id}', \App\Livewire\System\StaticSlider\Sliders::class)->name('static-slider.slides');

    /**deposit */
    Route::get('/deposit', DepositIndex::class)->name('deposit.index')->middleware(AbleTo::class . ":deposit_view");
    Route::get('/deposit/print', PrintSummery::class)->name('deposit.print-summery');


    Route::get('/comissions', ComissionsIndex::class)->name('comissions.index')->middleware(AbleTo::class . ":comission_view");
    Route::get('/comissions/take', Takes::class)->name('comissions.takes')->middleware(AbleTo::class . ":comission_view");
    Route::get('/comissions/{id}', TakesDetails::class)->name('comissions.details')->middleware(AbleTo::class . ":comission_view");
    Route::get('/comissions/takes/{id}', TakesDistributes::class)->name('comissions.distributes')->middleware(AbleTo::class . ":comission_confim");

    // Rotue::get()->name('comissions.');

    Route::post('/comissions/delete', [ProductComissionController::class, 'deleteComissions'])->name('comissions.destroy')->middleware(AbleTo::class . ":comission_delete");
    Route::delete('/comissions/reseller-profit/delete/{id}', [ProductComissionController::class, 'deleteResellerProfit'])->name('reseller-profit.destroy')->middleware(AbleTo::class . ":comission_delete");

    Route::post('/comissions/order/{id}', function ($id) {
        $cc = new ProductComissionController();
        $cc->dispatchProductComissionsListeners($id);
        // dd($cc);
        if (empty($cc)) {
            return redirect()->back()->with('success', 'Comission Confirmed');
        } else {
            return redirect()->back()->with('error', 'Have an error, see the log file');
        }
    })->name('comissions.confirm')->middleware(AbleTo::class . ":comission_confim");

    Route::post('/comissions/confirm/take/{id}', function ($id) {
        //
        // return 'hellow';
        try {

            $cc = new ProductComissionController();
            $cc->confirmSingleTakeComissions($id);
            return redirect()->back()->with('success', 'Comissions Confirmed!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th);
        }
    })->name('comissions.take.confirm')->middleware(AbleTo::class . ":comission_confim");


    Route::get('/comissions/refund/take/{id}', function ($id) {
        //
        try {

            $cc = new ProductComissionController();
            $cc->roleBackDistributedComissions($id);
            return redirect()->back()->with('success', 'Comissions Confirmed!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th);
        }
    })->name('comissions.take.refund')->middleware(AbleTo::class . ":comission_update");


    Route::get('/comissions/confirm/distribute/{id}', function ($id) {

        try {
            $dis = DistributeComissions::findOrFail($id);
            $dis->confirmed = true;
            $dis->save();
            return redirect()->back()->with('success', 'Comissions Confirmed!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th);
        }
    })->name('comissions.distribute.confirm')->middleware(AbleTo::class . ":comission_confim");

    Route::get('/comissions/refund/distribute/{id}', function ($id) {
        try {
            $dis = DistributeComissions::findOrFail($id);
            $dis->confirmed = false;
            $dis->save();
            return redirect()->back()->with('success', 'Comissions Confirmed!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th);
        }
    })->name('comissions.distribute.refund')->middleware(AbleTo::class . ":comission_update");



    /**
     * system order management
     */
    Route::prefix('orders')->name('orders.')->group(
        function () {
            Route::get('/', SystemOrdersIndex::class)->name('index');
            Route::get('/{id}', Details::class)->name('details');
        }
    )->middleware(AbleTo::class . ':order_view');

    Route::get('/print-summery', OrdersPrintSummery::class)->name('orders.sprint');

    /**
     * system withdraw
     */
    Route::prefix('withdraw')->group(
        function () {
            Route::get('/', WithdrawIndex::class)->name('withdraw.index');
            Route::get('/take{id}', WithdrawDetails::class)->name('withdraw.view');
            Route::get('/print', Pdf::class)->name('withdraw.print');
        }
    )->middleware(AbleTo::class . ":withdraw_view");


    // settings
    Route::get('/settings', SettingsIndex::class)->name('settings.index');
    Route::get('/pages', PagesIndex::class)->name('pages.index');
    Route::get('/pages/add-new', PagesCreate::class)->name('pages.create');
    // Route::get('/pages/{slug}', Pages::class);

    // branches
    Route::prefix('branches')->name('branches.')->group( function () {
        Route::get('/', BranchIndex::class)->name('index');
        Route::get('/create', BranchCreate::class)->name('create');
        Route::get('/edit/{branch}', BranchModify::class)->name('modify');
    });

    // partnership
    Route::prefix('partnership')->name('partnership.')->group( function() {
        Route::get('/', PartnershipIndex::class)->name('index');
        Route::get('/developer', PartnershipDeveloper::class)->name('developer');
        Route::get('/management', PartnershipManagement::class)->name('management');
    });


    // earn and sell
    Route::get('/earn/index', EarnBySellIndex::class)->name('earn.index');
    Route::get('/builder/footer', FooterBuilder::class)->name('footer.builder');

    Route::get('/reports', ReportIndex::class)->name('report.index');
    Route::get('/reports/generate', Report::class)->name('report.generate');

    /**
     * API Docs
     */
    Route::prefix('/api')->group(function () {

        Route::get('/', function () {
            return view('Api.start');
        })->name('api.index');

        Route::get('/auth', function () {
            return view('Api.auth');
        })->name('api.auth');
    });
    Route::get('/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');


    // geolocations
    // Rotue::get('/geolocations', \App\Livewire\System\Geolocation\Index::class)->name('geolocations.index')->middleware(AbleTo::class . ":geolocation_view");
    Route::get('/geolocations', \App\Livewire\System\Geolocation\Index::class)->name('geolocations.index');
    Route::get('/geolocations/countries', Countries::class)->name('geolocations.countries');
    Route::get('/geolocations/states', States::class)->name('geolocations.states');
    Route::get('/geolocations/cities', Cities::class)->name('geolocations.cities');
    Route::get('/geolocations/area', Area::class)->name('geolocations.area');


    // consignment
    Route::get('/consignment', ConsignmentIndex::class)->name('consignment.index');
});
