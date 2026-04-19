<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Packages;
use App\Models\vip;
use Inertia\Inertia;

class CheckoutController extends Controller
{


    public function index($id)
    {
        $package = Packages::with('payOption')->findOrFail($id);
        $ownerPackage = 1;

        return Inertia::render('User/Vip/Package/Checkout', [
            'package' => [
                'id' => $package->id,
                'name' => $package->name,
                'price' => $package->price,
                'coin' => $package->coin,
                'countdown' => $package->countdown,
                'description' => $package->description,
                'payOption' => $package->payOption->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'pay_type' => $item->pay_type,
                        'pay_to' => $item->pay_to,
                    ];
                }),
            ],
            'ownerPackage' => $ownerPackage,
        ]);
    }

    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'payment_by' => 'required',
            'trx' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'task_type' => 'required',
            'nid' => 'required',
            'nid_front' => 'required|image',
            'nid_back' => 'required|image',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 0;

        $validated['nid_front'] = $request->file('nid_front')->store('vips', 'public');
        $validated['nid_back'] = $request->file('nid_back')->store('vips', 'public');

        vip::create($validated);

        return redirect()->route('user.vip.index');
    }
}
