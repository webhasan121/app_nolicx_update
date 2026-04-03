<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Packages;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageIndexController extends Controller
{
    public function index()
    {
       $packages = Packages::all();
        return Inertia::render('User/Vip/Package/Index', [
            'packages' => $packages,
        ]);
    }
}
