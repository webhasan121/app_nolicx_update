<?php

use App\Http\Middleware\AbleTo;
use App\Models\cod;
use App\Livewire\Rider\Consignment\Index;
use App\Livewire\Rider\Consignment\View;
use App\Livewire\Rider\Dashboard as RiderDashboard;
use App\Livewire\Rider\RiderInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('consignments', RiderDashboard::class)->name('rider.consignment')->middleware(AbleTo::class . ':access_rider_dashboard');
// Route::get('my-consignments', Index::class)->name('rider.consignment');
Route::get('/consignments/{id}', View::class)->name('rider.consignment.view')->middleware(AbleTo::class . ':access_rider_dashboard');
Route::post('/consignments/{consignment}/status', function (Request $request, cod $consignment) {
    abort_unless($consignment->rider_id === auth()->id(), 403);

    if (auth()->user()->abailCoin() >= $consignment->total_amount) {
        $consignment->status = $request->string('status')->toString();
        $consignment->save();

        return back()->with('success', 'Shipment Updated');
    }

    return back()->with('warning', 'You do not have enough balance to process this request !');
})->name('rider.consignment.status')->middleware(AbleTo::class . ':access_rider_dashboard');
Route::get('/me', RiderInfo::class)->name('rider.me')->middleware(AbleTo::class . ':access_rider_dashboard');
