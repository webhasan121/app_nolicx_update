<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemUsersController;
use App\Http\Controllers\System\VendorController;
use App\Http\Controllers\User\CartCheckoutController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\DeveloperController;
use App\Http\Controllers\User\ManagementController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\PackageIndexController;
use App\Http\Controllers\User\ProfileEditController;
use App\Http\Controllers\User\RefController;
use App\Http\Controllers\User\UpgradeRiderController;
use App\Http\Controllers\User\UpgradeVendorCreateController;
use App\Http\Controllers\User\UpgradeVendorEditController;
use App\Http\Controllers\User\UpgradeVendorIndexController;
use App\Http\Controllers\User\VipController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\WalletComissionController;
use App\Http\Controllers\User\WalletDepositController;
use App\Http\Controllers\User\WalletRefferController;
use App\Http\Controllers\User\WalletTaskController;
use App\Http\Controllers\User\WithdrawCreateController;
use App\Http\Controllers\User\WithdrawIndexController;
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
use Inertia\Inertia;
use Livewire\Livewire;

use function Livewire\Volt\layout;

Route::middleware('guest')->group(function () {
    Volt::route('register/old', 'pages.auth.register')
        ->name('register');
    Route::get('/register', function () {
        return Inertia::render('Auth/Register', [
            'countries' => \App\Models\Country::orderBy('name')->get()
        ]);
    })->name('register');

    Route::get('/states/{country}', function ($country) {
        return \App\Models\State::where('country_id', $country)
            ->orderBy('name')
            ->get();
    });
    Route::get('/cities/{state}', function ($state) {
        return \App\Models\City::where('state_id', $state)
            ->orderBy('name')
            ->get();
    });
    Route::post('/register', [RegisteredUserController::class, 'store_user'])->name('register.store');



    Volt::route('login/old', 'pages.auth.login')
        ->name('login');

    Route::get('/login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'login_user']);


    Volt::route('forgot-password/old', 'pages.auth.forgot-password')
        ->name('password.request.old');
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'createReact'])
        ->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Volt::route('reset-password/{token}/old', 'pages.auth.reset-password')
        ->name('password.reset.old');
    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'createReact'])
        ->name('password.reset');
    Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email/old', 'pages.auth.verify-email')
        ->name('verification.notice.old');

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');



    Volt::route('confirm-password/old', 'pages.auth.confirm-password')
        ->name('password.confirm.old');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    volt::route('logout/old', 'pages.auth.logout')->name('logout.old');
    Route::get('logout', [AuthenticatedSessionController::class, 'logoutPage'])->name('logout');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.perform');




    // Route::middleware('verified')->group(function () {

    // route for user section
    Route::get('/user/index', function () {
        return redirect()->route('user.dash');
    })->name('user.index');

    Route::get('/users/edit/old', Edit::class)->name('edit.profile.old');
    Route::get('/users/edit', [ProfileEditController::class, 'edit'])->name('edit.profile');
    Route::get('/users/states/{country}', [ProfileEditController::class, 'loadStates'])->name('edit.profile.states');
    Route::get('/users/cities/{state}', [ProfileEditController::class, 'loadCities'])->name('edit.profile.cities');
    Route::post('/users/edit/profile', [ProfileEditController::class, 'updateProfile'])->name('edit.profile.update');
    Route::post('/users/edit/password', [ProfileEditController::class, 'updatePassword'])->name('edit.profile.password');
    Route::post('/users/edit/verification', [ProfileEditController::class, 'sendVerification'])->name('edit.profile.verification');

    Route::get('/user/old', userPanel::class)->name('user.dash.old');
    Route::get('/user', [\App\Http\Controllers\User\DashController::class, 'index'])->name('user.dash');
    Route::post('/user/check-ref', [\App\Http\Controllers\User\DashController::class, 'checkRef'])->name('user.check.ref');


    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/developer/old', UserDeveloper::class)->name('developer.old');
        Route::get('/developer', [DeveloperController::class, 'index'])->name('developer');
        Route::post('/developer/apply', [DeveloperController::class, 'apply'])->name('developer.apply');
        Route::get('/management/old', UserManagement::class)->name('management.old');
        Route::get('/management', [ManagementController::class, 'index'])->name('management');
        Route::post('/management/apply', [ManagementController::class, 'apply'])->name('management.apply');
    });

    Route::prefix('/user/upgrade')->group(function () {
        Route::get('/old', upgradeToVendorIndex::class)->name('upgrade.vendor.index.old');
        Route::get('/', [UpgradeVendorIndexController::class, 'index'])->name('upgrade.vendor.index');
        Route::get('/create/old', upgradeToVendorCreate::class)->name('upgrade.vendor.create.old');
        Route::get('/create', [UpgradeVendorCreateController::class, 'create'])->name('upgrade.vendor.create');
        Route::get('/cities/{state}', [UpgradeVendorCreateController::class, 'loadCities'])->name('upgrade.vendor.cities');
        Route::post('/store', [UpgradeVendorCreateController::class, 'store'])->name('upgrade.vendor.store');
        Route::get('/{id}/edit/old', upgradeToVendorEdit::class)->name('upgrade.vendor.edit.old');
        Route::get('/{id}/edit', [UpgradeVendorEditController::class, 'edit'])->name('upgrade.vendor.edit');
        Route::post('/{id}/update', [UpgradeVendorEditController::class, 'update'])->name('upgrade.vendor.update');
        Route::post('/{id}/update-document', [UpgradeVendorEditController::class, 'updateDocument'])->name('upgrade.vendor.updateDocument');

        Route::get('/rider/old', upgradeToRiderIndex::class)->name('upgrade.rider.index.old');
        Route::get('/rider', [UpgradeRiderController::class, 'index'])->name('upgrade.rider.index');
        Route::get('/rider/create/old', upgradeToRiderCreate::class)->name('upgrade.rider.create.old');
        Route::get('/rider/create', [UpgradeRiderController::class, 'create'])->name('upgrade.rider.create');
        Route::get('/rider/cities/{state}', [UpgradeRiderController::class, 'loadCities'])->name('upgrade.rider.cities');
        Route::get('/rider/areas/{city}', [UpgradeRiderController::class, 'loadAreas'])->name('upgrade.rider.areas');
        Route::post('/rider/store', [UpgradeRiderController::class, 'store'])->name('upgrade.rider.store');
        Route::get('/rider/{id}/edit/old', upgradeToRiderEdit::class)->name('upgrade.rider.edit.old');
        Route::get('/rider/{id}/edit', [UpgradeRiderController::class, 'edit'])->name('upgrade.rider.edit');
        Route::post('/rider/{id}/update', [UpgradeRiderController::class, 'update'])->name('upgrade.rider.update');
    });

    Route::prefix('user')->group(function () {
        Route::get('carts/old', Carts::class)->name('carts.view');
        Route::get('carts', [CartController::class, 'index'])->name('carts.view');

        Route::delete('carts/{id}', [CartController::class, 'destroy'])
            ->name('user.carts.remove');

        Route::get('orders/old', Orders::class)->name('user.orders.view');
        Route::get('orders', [OrderController::class, 'index'])
            ->name('user.orders.view');
        Route::get('orders/print', [OrderController::class, 'print'])
            ->name('user.orders.print');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])
            ->name('user.orders.delete');
        Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])
            ->name('user.orders.cancel');


        Route::get('orders/details/{id}/old', Details::class)->name('user.orders.details.old');
        Route::get('orders/details/{id}', [OrderController::class, 'details'])->name('user.orders.details');
        Route::post('orders/details/{id}/received', [OrderController::class, 'markReceived'])->name('user.orders.received');

        Route::get('carts/checkout/old', CartCheckout::class)->name('user.carts.checkout');
        Route::get('carts/checkout', [CartCheckoutController::class, 'index'])->name('user.carts.checkout');
        Route::post('carts/increase/{id}', [CartCheckoutController::class, 'increase'])->name('cart.qty.increase');
        Route::post('carts/decrease/{id}', [CartCheckoutController::class, 'decrease'])->name('cart.qty.decrease');
        Route::post('carts/confirm', [CartCheckoutController::class, 'confirm'])->name('user.carts.confirm');
        Route::get('cities/{state}', [CartCheckoutController::class, 'loadCities'])->name('load.cities');









        // vip
        Route::get('vip/old', Index::class)->name('user.vip.index');
        Route::get('vip', [VipController::class, 'index'])->name('user.vip.index');

        Route::get('vip/packages/old', PackageIndex::class)->name('user.vip.package');
        Route::get('vip/packages', [PackageIndexController::class, 'index'])->name('user.vip.package');

        Route::get('vip/packages/{id}/old', Checkout::class)->name('user.package.checkout');
        Route::get('vip/packages/{id}', [CheckoutController::class, 'index'])->name('user.package.checkout');
        Route::post('vip/package/purchase', [CheckoutController::class, 'purchase'])->name('user.package.purchase');
        // Route::get('vip/packages/{id}/cancle', function ($id) {
        //     dd(vip::find($id));
        // })->name('user.package.cancle');


        Route::get('/ref/old', Refs::class)->name('user.ref.view.old');
        Route::get('/ref', [RefController::class, 'index'])->name('user.ref.view');

        // user wallet
        Route::get('/wallet/old', WalletIndex::class)->name('user.wallet.index.old');
        Route::get('/wallet', [WalletController::class, 'index'])->name('user.wallet.index');
        Route::get('/wallet/print', [WalletController::class, 'print'])->name('user.wallet.print');

        Route::get('/wallet/comissions/earn/old', EarnComissions::class)->name('user.wallet.earn-comissions.old');
        Route::get('/wallet/comissions/earn', [WalletComissionController::class, 'index'])->name('user.wallet.earn-comissions');
        Route::get('/wallet/tasks/old', Task::class)->name('user.wallet.tasks.old');
        Route::get('/wallet/tasks', [WalletTaskController::class, 'index'])->name('user.wallet.tasks');
        // Route::get('/wallet/comissions/cut', SystemTakeComission::class)->name('user.wallet.system-comissions');
        Route::get('/wallet/reffer/vip/old', Reffer::class)->name('user.wallet.reffer.old');
        Route::get('/wallet/reffer/vip', [WalletRefferController::class, 'index'])->name('user.wallet.reffer');

        // user withdraw
        Route::get('/withdraw/old', WithdrawIndex::class)->name('user.wallet.withdraw.old');
        Route::get('/withdraw', [WithdrawIndexController::class, 'index'])->name('user.wallet.withdraw');
        Route::get('/withdraw/create/old', Create::class)->name('user.wallet.withdraw.create.old');
        Route::get('/withdraw/create', [WithdrawCreateController::class, 'index'])->name('user.wallet.withdraw.create');
        Route::post('/withdraw/store', [WithdrawController::class, 'storeFromUser'])->name('user.wallet.withdraw.store');
        Route::post('/withdraw/destroy', [WithdrawController::class, 'destroyFromUser'])->name('user.withdraw.destroy');

        // user deposit
        Route::get('/diposit/old', History::class)->name('user.wallet.diposit.old');
        Route::get('/diposit', [WalletDepositController::class, 'index'])->name('user.wallet.diposit');
        Route::get('/diposit/create/old', History::class)->name('user.wallet.diposit.create.old');
        Route::get('/diposit/create', [WalletDepositController::class, 'index'])->name('user.wallet.diposit.create');
        Route::post('/diposit/store', [WalletDepositController::class, 'store'])->name('user.wallet.diposit.store');


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
        Route::post('navigation', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'name' => ['required', 'string'],
            ]);

            auth()->user()->active_nav = $request->string('name')->toString();
            auth()->user()->save();

            return redirect()->route('dashboard');
        })->name('dashboard.navigation');


        include __DIR__ . '/system.php'; // include all routes for system

        include __DIR__ . '/vendor.php'; // include all route for vendor

        include __DIR__ . '/reseller.php'; // include all route for reseller

        // rider
        include __DIR__ . '/rider.php'; // include all route for rider

        // role and permission manage
        Route::get('roles/old', [RoleController::class, 'admin_list'])->name('system.role.list.old')->middleware(AbleTo::class . ':role_list');
        Route::get('roles', [RoleController::class, 'admin_list_react'])->name('system.role.list')->middleware(AbleTo::class . ':role_list');
        // Route::get('roles', roleIndexPage::class)->name('system.role.list')->middleware(AbleTo::class . ':role_list');
        Route::get('roles/edit/old', [RoleController::class, 'admin_edit'])->name('system.role.edit.old')->middleware(AbleTo::class . ":role_edit");
        Route::get('roles/edit', [RoleController::class, 'admin_edit_react'])->name('system.role.edit')->middleware(AbleTo::class . ":role_edit");
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
