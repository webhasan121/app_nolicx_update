<?php

use App\Http\Controllers\Rider\ConsignmentController;
use App\Http\Controllers\Rider\RiderInfoController;
use App\Http\Middleware\AbleTo;
use App\Models\cod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('consignments', [ConsignmentController::class, 'indexReact'])->name('rider.consignment')->middleware(AbleTo::class . ':access_rider_dashboard');
Route::post('/consignments/{order}/confirm', [ConsignmentController::class, 'confirmOrder'])->name('rider.consignment.confirm')->middleware(AbleTo::class . ':access_rider_dashboard');
// Route::get('my-consignments', Index::class)->name('rider.consignment');
Route::get('/consignments/{id}', [ConsignmentController::class, 'show'])->name('rider.consignment.view')->middleware(AbleTo::class . ':access_rider_dashboard');
Route::post('/consignments/{consignment}/status', function (Request $request, cod $consignment) {
    abort_unless($consignment->rider_id === auth()->id(), 403);

    if (auth()->user()->abailCoin() >= $consignment->total_amount) {
        $consignment->status = $request->string('status')->toString();
        $consignment->save();

        return back()->with('success', 'Shipment Updated');
    }

    return back()->with('warning', 'You do not have enough balance to process this request !');
})->name('rider.consignment.status')->middleware(AbleTo::class . ':access_rider_dashboard');
Route::get('/me', [RiderInfoController::class, 'show'])->name('rider.me')->middleware(AbleTo::class . ':access_rider_dashboard');
