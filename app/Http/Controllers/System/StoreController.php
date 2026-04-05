<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\TakeComissions;
use App\Models\Withdraw;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function indexReact()
    {
        return Inertia::render('Auth/system/store/index', [
            'coinStore' => [
                'store' => TakeComissions::where(['confirmed' => true])->sum('store'),
                'take' => TakeComissions::where(['confirmed' => true])->sum('take_comission'),
                'give' => TakeComissions::where(['confirmed' => true])->sum('distribute_comission'),
            ],
            'coastStore' => [
                'store' => Withdraw::where(['status' => true])->sum('maintenance_fee'),
            ],
            'donationStore' => [
                'store' => Withdraw::where(['status' => true])->sum('server_fee'),
            ],
        ]);
    }
}
