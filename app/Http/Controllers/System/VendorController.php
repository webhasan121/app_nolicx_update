<?php

namespace App\Http\Controllers\System;

use App\Http\Middleware\AbleTo;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\HandleVendor;
use App\Http\Middleware\Owner;
use App\Models\vendor;
use App\Models\vendor_has_document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Can;
use Spatie\Permission\Models\Permission;

class VendorController extends Controller
{

    use HandleVendor;


    public function __construct()
    {
        $this->middleware(AbleTo::class . ":vendors_view")->only('viewToDashboard');
        $this->middleware(AbleTo::class . ":vendors_edit")->only('edit');

        // vendor permission for product
        $this->middleware(AbleTo::class . ":product_view")->only('productView');
        $this->middleware(AbleTo::class . ":product_add")->only('productStore');
        $this->middleware(AbleTo::class . ":product_edit")->only('productEdit');
        $this->middleware(AbleTo::class . ":product_update")->only('productUpdate');

        // vendor permission for category
        $this->middleware(AbleTo::class . ":category_view")->only('categoryList');
        $this->middleware(AbleTo::class . ":category_add")->only('categoryStore');
        $this->middleware(AbleTo::class . ":category_edit")->only('categoryEdit');
        $this->middleware(AbleTo::class . ":category_update")->only('categoryUpdate');

        // $this->middleware(Owner::class . "")->only('upgradeEdit');

        // vendor permission for order 
    }

    public function upgradeIndex()
    {
        // dd(auth()->user()->requestsToBeVendor()->where(['status' => 'Pending'])->count());
        return view('user.pages.profile-upgrade.vendor.index');
    }
    public function upgradeCreateRequest()
    {
        return view('user.pages.profile-upgrade.vendor.create');
    }
    public function upgradeStore(Request $request)
    {
        // if (auth()->user()->requestsToBeVendor()->where(['status' => 'Pending'])->count()) {
        //     return redirect()->back()->with('info', 'One Request Has Been Pending!');
        // }

        // dd($request->all());
        $request->validate([
            // unique, but ignore when upgate 
            'shop_name_en' => ['required', 'string', 'max:100', 'min:5', 'unique:vendors'],
            'shop_name_bn' => 'required',
            'phone' => ['required', 'max:11', 'min:10', 'unique:vendors'],
            'email' => [
                'required',
                'email',
                'unique:vendors'
            ],
            'country' => 'required',
            'district' => 'required',
            'upozila' => 'required',
            'village' => 'required',
            'zip' => ['required', 'integer'],
            'road_no' => 'required',
            'house_no' => 'required',
        ]);
        $request->mergeIfMissing(['slug' => Str::slug($request->shop_name_en)]); // slug

        $vendorId = vendor::create($request->except('_token'));
        // dd();
        return redirect()->route('upgrade.vendor.edit', ['id' => $vendorId->id]);
        // $this->vendor()->update([]);
    }

    public function upgradeEdit($id)
    {
        /**
         * user can able to update the rquest,
         * if the request is pending
         */
        // $vendor = vendor::find($id);
        // if ($vendor->status == 'Pending') {


        $data = auth()->user()->requestsToBeVendor()->find($id);

        if ($data && $data->status == 'Pending') {
            return view('user.pages.profile-upgrade.vendor.edit', compact('data'), ['vendor' => 'active']);
        } else {
            return redirect()->back()->with('warning', 'You are unable to edit or Update!');
        }
    }

    public function upgradeUpdate($id)
    {
        // 
        $data = auth()->user()->requestsToBeVendor()->find($id);
        if ($data && $data->status == 'Pending') {
            $data->update(request()->except('_token'));
            return redirect()->back()->with('success', 'Request Updated!');
        } else {
            return redirect()->back()->with('warning', 'Unable to Edit or Update');
        }
    }

    public function upgradeUpdateDocument($id)
    {
        // 
        // $data = auth()->user()->requestsToBeVendor()->find($id);
        $data = vendor_has_document::find($id);
        // dd($data->vendorRequest->status);
        if ($data && $data->vendorRequest->status == 'Pending') {
            $data->update(request()->except('_token'));
            return redirect()->back()->with('success', 'Request Updated!');
        } else {
            return redirect()->back()->with('warning', 'Unable to Edit or Update');
        }
    }




    /**
     * Vendor list at system dashboard
     */
    public function viewToDashboard()
    {
        // $perm = 'role_view';
        $filter = request('filter') ?? 'Active';

        // switch ($filter) {
        //     case 'Active':
        //         # code...
        //         break;

        //     default:
        //         # code...
        //         break;
        // }
        $vendors = vendor::where(['status' => 'Pending'])->orderBy('id', 'desc')->get();
        return view('auth.system.vendors.index', compact('vendors'));
    }

    /**
     * vendor edit 
     */
    public function edit($id)
    {
        $vendor = vendor::find($id);
        return view('auth.system.vendors.edit', compact('vendor'));
    }

    public function viewSettings($id)
    {
        $vendor = vendor::find($id);
        $permissions = Permission::all();
        return view('auth.system.vendors.vendor.settings', compact('vendor', 'permissions'));
    }
    public function viewProducts($id)
    {
        $vendor = vendor::find($id);
        return view('auth.system.vendors.vendor.products', compact('vendor'));
    }
    public function viewOrders($id)
    {
        $vendor = vendor::find($id);
        return view('auth.system.vendors.vendor.orders', compact('vendor'));
    }
    public function viewDocuments($id)
    {
        $vendor = vendor::find($id);
        return view('auth.system.vendors.vendor.documents', compact('vendor'));
    }
    public function viewCategories($id)
    {
        $vendor = vendor::find($id);
        return view('auth.system.vendors.vendor.categories');
    }

    public function updateBySystem($id)
    {
        // dd(request()->except(['id', '_token']));
        $data = vendor::find($id);
        // dd($data->id);

        if ($data) {
            // dd(request()->except(['id', '_token']));
            $data->update(request()->except(['id', '_token']));
        }

        return redirect()->back()->with('success', 'Vendor Data Updated!');
    }
}
