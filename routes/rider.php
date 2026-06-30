<?php

use App\Http\Controllers\Rider\ConsignmentController;
use App\Http\Controllers\Rider\RiderInfoController;
use App\Http\Middleware\AbleTo;
use App\Models\cod;
use App\Models\syncOrder;
use App\Support\OrderNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('consignments', [ConsignmentController::class, 'indexReact'])->name('rider.consignment')->middleware(AbleTo::class . ':access_rider_dashboard');
Route::post('/consignments/{order}/confirm', [ConsignmentController::class, 'confirmOrder'])->name('rider.consignment.confirm')->middleware(AbleTo::class . ':access_rider_dashboard');
// Route::get('my-consignments', Index::class)->name('rider.consignment');
Route::get('/consignments/{id}', [ConsignmentController::class, 'show'])->name('rider.consignment.view')->middleware(AbleTo::class . ':access_rider_dashboard');
Route::post('/consignments/{consignment}/status', function (Request $request, cod $consignment) {
    abort_unless((int) $consignment->rider_id === (int) auth()->id(), 403);

    $payload = $request->validate([
        'status' => ['required', 'string', 'in:Received,Completed'],
    ]);

    if (auth()->user()->abailCoin() >= $consignment->total_amount) {
        $consignment->status = $payload['status'];
        $consignment->save();
        $order = $consignment->order;
        OrderNotice::riderStatusChanged($order, $consignment->status, auth()->id());

        if ($order->exists) {
            if ($consignment->status === 'Received') {
                $order->update(['status' => 'Delivery']);
                OrderNotice::statusChanged($order, 'Delivery', auth()->id());
                $synced = syncOrder::query()->where('reseller_order_id', $order->id)->first();
                if ($synced && $synced->status !== 'Delivery') {
                    $synced->status = 'Delivery';
                    $synced->save();
                }
            }

            if ($consignment->status === 'Completed') {
                $order->update(['status' => 'Delivered']);
                OrderNotice::statusChanged($order, 'Delivered', auth()->id());
                $synced = syncOrder::query()->where('reseller_order_id', $order->id)->first();
                if ($synced && $synced->status !== 'Delivered') {
                    $synced->status = 'Delivered';
                    $synced->save();
                }
            }
        }

        return back()->with('success', 'Shipment Updated');
    }

    return back()->with('warning', 'You do not have enough balance to process this request !');
})->name('rider.consignment.status')->middleware(AbleTo::class . ':access_rider_dashboard');
Route::get('/me', [RiderInfoController::class, 'show'])->name('rider.me')->middleware(AbleTo::class . ':access_rider_dashboard');
