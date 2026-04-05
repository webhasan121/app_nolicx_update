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
use App\Models\Product;
use App\Models\vendor;
use App\Models\vendor_has_document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
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

    public function indexReact(Request $request)
    {
        $filter = $request->input('filter', 'Active');
        $find = $request->input('find');

        $query = vendor::query()
            ->with('user')
            ->orderBy('id', 'desc');

        if ($filter !== '*') {
            $query->where('status', $filter);
        }

        if (!empty($find)) {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('shop_name_en', 'like', '%' . $find . '%')
                    ->orWhereHas('user', function ($userQuery) use ($find) {
                        $userQuery->where('name', 'like', '%' . $find . '%');
                    });
            });
        }

        $vendors = $query->get()->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'user_name' => $vendor->user?->name ?? 'N/A',
                'shop_name_en' => $vendor->shop_name_en ?? 'N/A',
                'email' => $vendor->user?->email ?? 'N/A',
                'phone' => $vendor->user?->phone ?? 'N/A',
                'location' => collect([
                    $vendor->user?->upazila ?? 'N/A',
                    $vendor->user?->district ?? 'N/A',
                    $vendor->user?->country ?? 'N/A',
                ])->join(', '),
                'status' => $vendor->status ?? 'N/A',
                'system_get_comission' => $vendor->system_get_comission ?? 'N/A',
                'products_count' => Product::query()
                    ->vendor()
                    ->where('user_id', $vendor->user_id)
                    ->count(),
                'created_at_formatted' => $vendor->created_at?->toFormattedDateString(),
            ];
        })->values()->all();

        return Inertia::render('Auth/system/vendors/index', [
            'filter' => $filter,
            'find' => $find,
            'widgets' => [
                ['title' => 'Total Vendor', 'content' => vendor::query()->count()],
                ['title' => 'Pending', 'content' => vendor::query()->pending()->count()],
                ['title' => 'Active', 'content' => vendor::query()->active()->count()],
                ['title' => 'Disabled', 'content' => vendor::query()->disabled()->count()],
                ['title' => 'Suspended', 'content' => vendor::query()->suspended()->count()],
            ],
            'vendors' => $vendors,
        ]);
    }

    /**
     * vendor edit 
     */
    public function edit($id)
    {
        $vendor = vendor::find($id);
        return view('auth.system.vendors.edit', compact('vendor'));
    }

    public function editReact($id)
    {
        $vendor = vendor::with('user.getReffOwner.owner')->findOrFail($id);
        $user = $vendor->user;

        return Inertia::render('Auth/system/vendors/Edit', [
            'vendor' => [
                'id' => $vendor->id,
                'user' => [
                    'id' => $vendor->user?->id,
                    'name' => $vendor->user?->name ?? 'N/A',
                    'email' => $vendor->user?->email ?? 'N/A',
                    'phone' => $vendor->user?->phone ?? 'N/A',
                ],
                'shop_name_en' => $vendor->shop_name_en ?? 'N/A',
                'shop_name_bn' => $vendor->shop_name_bn ?? 'N/a',
                'status' => $vendor->status ?? 'N/A',
                'created_at_formatted' => $vendor->created_at?->toFormattedDateString() ?? '',
            ],
            'editUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'coin' => $user->coin,
                'reference' => $user->reference,
                'reference_owner_name' => $user->getReffOwner?->owner?->name,
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions' => $user->getPermissionNames()->values()->all(),
                'permissions_via_role' => $user->getPermissionsViaRoles()->pluck('name')->values()->all(),
            ],
            'roles' => \Spatie\Permission\Models\Role::query()->get(['id', 'name'])->toArray(),
            'permissions' => \Spatie\Permission\Models\Permission::query()->get(['id', 'name'])->toArray(),
            'defaultAdminRef' => config('app.ref'),
        ]);
    }

    public function viewSettings($id)
    {
        $vendor = vendor::find($id);
        $permissions = Permission::all();
        return view('auth.system.vendors.vendor.settings', compact('vendor', 'permissions'));
    }

    public function settingsReact($id)
    {
        $vendor = vendor::with('user')->findOrFail($id);

        return Inertia::render('Auth/system/vendors/Settings', [
            'vendor' => [
                'id' => $vendor->id,
                'user' => [
                    'id' => $vendor->user?->id,
                    'name' => $vendor->user?->name ?? 'N/A',
                    'email' => $vendor->user?->email ?? 'N/A',
                    'phone' => $vendor->user?->phone ?? 'N/A',
                ],
                'shop_name_en' => $vendor->shop_name_en ?? 'N/A',
                'shop_name_bn' => $vendor->shop_name_bn ?? 'N/a',
                'status' => $vendor->status ?? 'N/A',
                'created_at_formatted' => $vendor->created_at?->toFormattedDateString() ?? '',
                'email' => $vendor->email ?? 'N/A',
                'phone' => $vendor->phone ?? 'N/A',
                'address' => $vendor->address ?? 'N/A',
                'upazila' => $vendor->upazila ?? 'N/A',
                'district' => $vendor->district ?? 'N/A',
                'country' => $vendor->country ?? 'N/A',
                'system_get_comission' => $vendor->system_get_comission ?? 0,
                'allow_max_product_upload' => $vendor->allow_max_product_upload ?? 0,
                'max_product_upload' => $vendor->max_product_upload ?? '',
                'can_resell_products' => $vendor->can_resell_products ?? 0,
                'is_rejected' => $vendor->is_rejected ?? 0,
                'rejected_for' => $vendor->rejected_for ?? '',
            ],
        ]);
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

    public function documentsReact($id)
    {
        $vendor = vendor::with(['user', 'documents'])->findOrFail($id);
        $document = $vendor->documents;
        $deadline = $document?->deatline ? Carbon::parse($document->deatline) : null;

        return Inertia::render('Auth/system/vendors/Documents', [
            'vendor' => [
                'id' => $vendor->id,
                'user' => [
                    'id' => $vendor->user?->id,
                    'name' => $vendor->user?->name ?? 'N/A',
                    'email' => $vendor->user?->email ?? 'N/A',
                    'phone' => $vendor->user?->phone ?? 'N/A',
                ],
                'shop_name_en' => $vendor->shop_name_en ?? 'N/A',
                'shop_name_bn' => $vendor->shop_name_bn ?? 'N/a',
                'status' => $vendor->status ?? 'N/A',
                'created_at_formatted' => $vendor->created_at?->toFormattedDateString() ?? '',
                'documents' => [
                    'deatline' => $deadline?->format('Y-m-d'),
                    'deatline_formatted' => $deadline?->toFormattedDateString(),
                    'deatline_human' => $deadline?->diffForHumans(),
                    'nid' => $document?->nid,
                    'nid_front_url' => $document?->nid_front ? asset('storage/' . $document->nid_front) : null,
                    'nid_back_url' => $document?->nid_back ? asset('storage/' . $document->nid_back) : null,
                    'shop_tin' => $document?->shop_tin,
                    'shop_tin_image_url' => $document?->shop_tin_image ? asset('storage/' . $document->shop_tin_image) : null,
                    'shop_trade' => $document?->shop_trade,
                    'shop_trade_image_url' => $document?->shop_trade_image ? asset('storage/' . $document->shop_trade_image) : null,
                ],
            ],
        ]);
    }

    public function updateDocumentDeadline(Request $request, $id)
    {
        $request->validate([
            'deatline' => ['required', 'date'],
        ]);

        $vendor = vendor::with('documents')->findOrFail($id);
        $vendor->documents?->update([
            'deatline' => $request->deatline,
        ]);

        return redirect()->back()->with('success', 'Updated');
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
