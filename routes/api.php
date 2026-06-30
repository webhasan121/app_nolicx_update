<?php

use App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FcmDeviceTokenController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PartnershipController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserCartController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\UserOrderController;
use App\Http\Controllers\Api\UserVipController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('comments', [CommentController::class, 'index']);

Route::get('products/for-you', [ProductController::class, 'forYou']);
Route::get('products/today', [ProductController::class, 'today']);
Route::get('products/{product}', [ProductController::class, 'show']);

Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}/products', [CategoryController::class, 'products']);

Route::get('countries', [LocationController::class, 'countries']);
Route::get('states', [LocationController::class, 'states']);
Route::get('cities', [LocationController::class, 'cities']);

Route::controller(Auth::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
});

Route::prefix('auth')->controller(Auth::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', 'me');
        Route::post('logout', 'logout');
        Route::post('email/verification-notification', 'sendVerification');
        Route::post('verify-email', 'verify');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('fcm/token', [FcmDeviceTokenController::class, 'store']);
    Route::delete('fcm/token', [FcmDeviceTokenController::class, 'destroy']);

    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);

    Route::get('developer', [PartnershipController::class, 'developer']);
    Route::post('developer/apply', [PartnershipController::class, 'applyDeveloper']);
    Route::get('management', [PartnershipController::class, 'management']);
    Route::post('management/apply', [PartnershipController::class, 'applyManagement']);

    Route::get('wallet', [WalletController::class, 'index']);
    Route::post('withdraw/request', [WalletController::class, 'withdrawRequest']);
    Route::get('withdraw/history', [WalletController::class, 'withdrawHistory']);
    Route::get('withdraw', [WalletController::class, 'withdrawIndex']);
    Route::get('withdraw/create', [WalletController::class, 'withdrawCreate']);
    Route::post('withdraw/store', [WalletController::class, 'withdrawRequest']);
    Route::post('withdraw/destroy', [WalletController::class, 'withdrawCancel']);

    Route::post('comments', [CommentController::class, 'store']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
    Route::post('comments/{comment}/delete', [CommentController::class, 'destroy']);

    Route::prefix('user')->group(function () {
        Route::post('fcm/token', [FcmDeviceTokenController::class, 'store']);
        Route::delete('fcm/token', [FcmDeviceTokenController::class, 'destroy']);

        Route::get('/', [UserDashboardController::class, 'index']);
        Route::get('ref', [UserDashboardController::class, 'ref']);
        Route::post('check-ref', [UserDashboardController::class, 'checkRef']);

        Route::get('wallet', [WalletController::class, 'index']);
        Route::get('withdraw', [WalletController::class, 'withdrawIndex']);
        Route::get('withdraw/create', [WalletController::class, 'withdrawCreate']);
        Route::post('withdraw/store', [WalletController::class, 'withdrawRequest']);
        Route::post('withdraw/destroy', [WalletController::class, 'withdrawCancel']);

        Route::get('vip', [UserVipController::class, 'index']);
        Route::get('vip/packages', [UserVipController::class, 'packages']);
        Route::get('vip/packages/{package}', [UserVipController::class, 'packageDetails']);
        Route::post('vip/package/purchase', [UserVipController::class, 'purchase']);
        Route::get('vip/package/time', [UserVipController::class, 'time']);
        Route::post('vip/package/time-count', [UserVipController::class, 'countTime']);

        Route::get('carts', [UserCartController::class, 'index']);
        Route::post('carts', [UserCartController::class, 'store']);
        Route::delete('carts/{id}', [UserCartController::class, 'destroy']);
        Route::get('carts/checkout', [UserCartController::class, 'checkout']);
        Route::post('carts/checkout', [UserCartController::class, 'confirm']);
        Route::post('carts/confirm', [UserCartController::class, 'confirm']);
        Route::post('carts/increase/{id}', [UserCartController::class, 'increase']);
        Route::post('carts/decrease/{id}', [UserCartController::class, 'decrease']);
        Route::get('cities/{state}', [UserCartController::class, 'cities']);

        Route::get('orders', [UserOrderController::class, 'index']);
        Route::post('orders', [UserOrderController::class, 'store']);
        Route::get('orders/{id}', [UserOrderController::class, 'show']);
        Route::delete('orders/{order}', [UserOrderController::class, 'destroy']);
        Route::patch('orders/{order}/cancel', [UserOrderController::class, 'cancel']);
        Route::post('orders/{id}/received', [UserOrderController::class, 'markReceived']);

        Route::get('Order', [UserOrderController::class, 'index']);
        Route::post('Order', [UserOrderController::class, 'store']);
        Route::get('Order/{id}', [UserOrderController::class, 'show']);
        Route::delete('Order/{order}', [UserOrderController::class, 'destroy']);
    });

});
