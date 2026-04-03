<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemUsersController;
use App\Http\Controllers\System\VendorController;
use App\Http\Controllers\WithdrawController;
use App\Livewire\Actions\Logout;
use App\Http\Middleware\AbleTo;
use App\Livewire\Profile\Edit;
use Livewire\Volt\Volt;


use App\Livewire\System\Roles\Index as roleIndexPage;
use App\Livewire\System\Users\Index as userIndexPage;
use App\Livewire\User\CartCheckout;
use App\Livewire\User\Carts;
use App\Livewire\User\Dash as userPanel;
use App\Livewire\User\Order\Details;
use App\Livewire\User\Orders;
use App\Livewire\User\Refs;
use App\Livewire\User\Upgrade\Vendor\Index as upgradeToVendorIndex;
use App\Livewire\User\Upgrade\Vendor\Create as upgradeToVendorCreate;
use App\Livewire\User\Upgrade\Vendor\Edit as upgradeToVendorEdit;

use App\Livewire\User\Upgrade\Rider\Index as upgradeToRiderIndex;
use App\Livewire\User\Upgrade\Rider\Edit as upgradeToRiderEdit;
use App\Livewire\User\Upgrade\Rider\Create as upgradeToRiderCreate;
use App\Livewire\User\Vip\Index;
use App\Livewire\User\Vip\Package\Checkout;
use App\Livewire\User\Vip\Package\Index as PackageIndex;
use App\Livewire\User\Vip\Package\Purchase;
use App\Livewire\User\Wallet\Index as WalletIndex;
use App\Livewire\User\Wallet\Withdraw\Create;
use App\Livewire\User\Wallet\Withdraw\Index as WithdrawIndex;
use App\Livewire\User\Wallet\Comission as EarnComissions;
use App\Livewire\User\Wallet\Diposit\History;
use App\Livewire\User\Wallet\Reffer;
use app\Livewire\User\Wallet\SystemTakeComission;
use App\Livewire\User\Wallet\Task;
use App\Livewire\User\Partnership\Developer as UserDeveloper;
use App\Livewire\User\Partnership\Management as UserManagement;
use App\Models\Products_has_comments;
use App\Models\User;
use App\Models\vip;
use Livewire\Livewire;

use function Livewire\Volt\layout;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');

    volt::route('logout', 'pages.auth.logout')->name('logout');


    // Route::middleware('verified')->group(function () {

    // route for user section 
    Route::get('/user/index', function () {
        return redirect()->route('user.dash');
    })->name('user.index');

    Route::get('/users/edit', Edit::class)->name('edit.profile');

    Route::get('/user', userPanel::class)->name('user.dash');

    Route::prefix('user')->name('user.')->group( function() {
        Route::get('/developer', UserDeveloper::class)->name('developer');
        Route::get('/management', UserManagement::class)->name('management');
    });

    Route::prefix('/user/upgrade')->group(function () {
        Route::get('/', upgradeToVendorIndex::class)->name('upgrade.vendor.index');
        Route::get('/create', upgradeToVendorCreate::class)->name('upgrade.vendor.create');
        Route::post('/store', [VendorController::class, 'upgradeStore'])->name('upgrade.vendor.store');
        Route::get('/{id}/edit', upgradeToVendorEdit::class)->name('upgrade.vendor.edit');
        Route::post('/{id}/update', [VendorController::class, 'upgradeUpdate'])->name('upgrade.vendor.update');
        Route::post('/{id}/update-document', [VendorController::class, 'upgradeUpdateDocument'])->name('upgrade.vendor.updateDocument');


        Route::get('/rider', upgradeToRiderIndex::class)->name('upgrade.rider.index');
        Route::get('/rider/create', upgradeToRiderCreate::class)->name('upgrade.rider.create');
        Route::get('/rider/{id}/edit', upgradeToRiderEdit::class)->name('upgrade.rider.edit');
    });

    Route::prefix('user')->group(function () {
        Route::get('carts', Carts::class)->name('carts.view');
        Route::get('orders', Orders::class)->name('user.orders.view');
        Route::get('orders/details/{id}', Details::class)->name('user.orders.details');

        Route::get('carts/checkout', CartCheckout::class)->name('user.carts.checkout');

        // vip 
        Route::get('vip', Index::class)->name('user.vip.index');
        Route::get('vip/packages', PackageIndex::class)->name('user.vip.package');
        Route::get('vip/packages/{id}', Checkout::class)->name('user.package.checkout');
        // Route::get('vip/packages/{id}/cancle', function ($id) {
        //     dd(vip::find($id));
        // })->name('user.package.cancle');


        Route::get('/ref', Refs::class)->name('user.ref.view');

        // user wallet
        Route::get('/wallet', WalletIndex::class)->name('user.wallet.index');
        Route::get('/wallet/comissions/earn', EarnComissions::class)->name('user.wallet.earn-comissions');
        Route::get('/wallet/tasks', Task::class)->name('user.wallet.tasks');
        // Route::get('/wallet/comissions/cut', SystemTakeComission::class)->name('user.wallet.system-comissions');
        Route::get('/wallet/reffer/vip', Reffer::class)->name('user.wallet.reffer');

        // user withdraw 
        Route::get('/withdraw', WithdrawIndex::class)->name('user.wallet.withdraw');
        Route::get('/withdraw/create', Create::class)->name('user.wallet.withdraw.create');
        Route::post('/withdraw/store', [WithdrawController::class, 'storeFromUser'])->name('user.wallet.withdraw.store');

        // user deposit 
        Route::get('/diposit', History::class)->name('user.wallet.diposit');
        Route::get('/diposit/create', History::class)->name('user.wallet.diposit.create');


        /**
         * add comment to product
         */

        Route::post('/products/comments', [ProductController::class, 'storeComment'])->name('user.comment.store');
        Route::post('/products/comments/{id}/destroy', function ($id) {
            try {
                //code...
                Products_has_comments::destroy($id);
            } catch (\Throwable $th) {
                //throw $th;
                return redirect()->back()->with('error', $th->getMessage());
            }
            return redirect()->back();
        })->name('user.comment.destroy');
    });

    Route::prefix('dashboard')->group(function () {


        include('system.php'); // include all routes for system

        include('vendor.php'); // include all route for vendor

        include('reseller.php'); // include all route for reseller

        // rider 
        include('rider.php'); // include all route for rider

        // role and permission manage
        Route::get('roles', [RoleController::class, 'admin_list'])->name('system.role.list')->middleware(AbleTo::class . ':role_list');
        // Route::get('roles', roleIndexPage::class)->name('system.role.list')->middleware(AbleTo::class . ':role_list');
        Route::get('roles/edit', [RoleController::class, 'admin_edit'])->name('system.role.edit')->middleware(AbleTo::class . ":role_edit");
        Route::post('role-to-users', [RoleController::class, 'multiple_user_to_single_role'])->name('system.role.to-user')->middleware(AbleTo::class . ':sync_role_to_user'); // single role to multiple users

        /**
         * user to role
         */
        Route::post('user-to-roles', [RoleController::class, 'multiple_role_to_single_user'])->name('multiple_role_to_single_user')->middleware(AbleTo::class . ':sync_role_to_user'); // multiple role to single user
        Route::post('permissions/{role}/to-role', [RoleController::class, 'system_give_permission_to_role'])->name('system.permissions.to-role')->middleware(AbleTo::class . ':sync_permission_to_role');
        Route::post('permissions/{user}/to-user', [RoleController::class, 'system_give_permission_to_user'])->name('system.permissions.to-user')->middleware(AbleTo::class . ':sync_permission_to_role');

        // Route::get('/comissions')->name('comissions');


        // route for rider 


    });
});
