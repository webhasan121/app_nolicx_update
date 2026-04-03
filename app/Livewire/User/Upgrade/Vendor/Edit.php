<?php

namespace App\Livewire\User\Upgrade\Vendor;

use App\Models\reseller;
use App\Models\reseller_has_document;
use App\Models\vendor;
use App\Models\vendor_has_document;
use Carbon\Carbon;
use Illuminate\Foundation\Exceptions\Renderer\Listener;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use App\HandleImageUpload;

class Edit extends Component
{
    use WithFileUploads, HandleImageUpload;

    #[URL]
    public $id, $upgrade = 'vendor', $nav = 'basic';

    private $data;

    // listeners for refresh event 
    protected $listener = ['refresh' => 'refresh'];


    /**
     * component data
     */
    public $vendor, $newLogo, $newBanner, $vendorDocument = [], $nid_front, $nid_back, $shop_tin_image, $shop_trade_image;


    public function mount()
    {
        $this->getDate();
        // dd($this->data);

        // if (empty($this->vendor) || empty($this->vendorDocument)) {
        //     $this->redirectIntended(route('upgrade.vendor.index', ['upgrade' => $this->upgrade]), true);
        // }
    }


    // equest()->all()

    // public function updated($property)
    // {
    //     if ($property == $this->vendorDocument['nid_front']) {
    //         $this->nid_front == $this->vendorDocument['nid_front'];;
    //     }
    // }

    public function getDate()
    {
        if ($this->upgrade == 'reseller') {
            // if upgrade is reseller, get the reseller data
            $this->data = auth()->user()->requestsToBeReseller()->find($this->id);
        } else {
            $this->data = auth()->user()->requestsToBeVendor()->find($this->id);
        }

        $this->vendor = $this->data?->toArray();
        $this->vendorDocument = $this->data?->documents?->toArray();

        // if ($this->data?->status != 'Pending' || $this->data?->status == 'Suspended'  || ($this->data?->status != 'Disabled' && $this->data?->documents?->deatline < Carbon::now())) {
        //     $this->dispatch('info', 'Unable to process');
        //     session()->flash('info', 'Unable to Edit or Update');
        //     $this->redirectIntended(route('upgrade.vendor.index'), true);
        // }
    }


    public function update()
    {
        if (!empty($this->newLogo)) {
            $this->vendor['logo'] = $this->handleImageUpload($this->newLogo, 'shop-logo', $this->vendor['logo']);
        }

        if (!empty($this->newBanner)) {
            $this->vendor['banner'] = $this->handleImageUpload($this->newBanner, 'shop-banner', $this->vendor['banner']);
        }
        // dd(request()->all());

        if ($this->upgrade == 'reseller') {
            reseller::find($this->id)->update($this->vendor);
        } else {
            vendor::find($this->id)->update($this->vendor);
        }
        // Session::flash('success', 'Your vendor request updated !');
        $this->dispatch('refresh');
        $this->dispatch('alert', 'Updated');
    }

    public function updateDocument()
    {
        // dd($this->vendorDocument);

        if ($this->upgrade == 'vendor') {
            $vd = vendor_has_document::find($this->vendorDocument['id']);
        } else {
            $vd = reseller_has_document::find($this->vendorDocument['id']);
        }

        // $this->validate(
        //     [vendorDocument
        //         'vendorDocument.nid_front' => ['required' | 'image' | 'max:1024'], // max 1MB 
        //         'vendorDocument.nid_back' => ['image' | 'max:1024'], // max 1MB
        //         'vendorDocument.shop_trade_image' => [
        //             'image' | 'max:1024'
        //         ], // 1 MB
        //         'vendorDocument.shop_tin_image' =>
        //         [
        //             'image' | 'max:1024' | 'mim:png, jpg'
        //         ], // 1 MB
        //     ]
        // );
        // $this->vendorDocument->nid_front->store(path: 'vendor-document');

        // $nid_image_front = 'vendor-nid-front-' . time() . '.' . $this->vendorDocument['nid_front']->getClientOriginalExtension();
        // dd($this->processImageStore($this->nid_front, 'vendor-document', 'vendor-nid-front-'));
        $data = [
            'nid' => $this->vendorDocument['nid'],
            'shop_tin' => $this->vendorDocument['shop_tin'],
            'shop_trade' => $this->vendorDocument['shop_trade'],

            // 'shop_tin_image' => $this->processImageStore($this->shop_tin_image, 'vendor-document', 'vendor-shop-tin-'),
            // 'shop_trade_image' =>  $this->processImageStore($this->shop_trade_image, 'vendor-document', 'vendor-shop-trade-'),
            // 'nid_front' => $this->processImageStore($this->nid_front, 'vendor-document', 'vendor-nid-front-'),
            // 'nid_back' => $this->processImageStore($this->nid_back, 'vendor-document', 'vendor-nid-back-'),
        ];


        if ($this->shop_tin_image) {
            $data['shop_tin_image'] = $this->handleImageUpload($this->shop_tin_image, 'upgrade-document', $this->vendorDocument['shop_tin_image']);
        }
        if ($this->shop_trade_image) {
            $data['shop_trade_image'] = $this->handleImageUpload($this->shop_trade_image, 'upgrade-document', $this->vendorDocument['shop_trade_image']);
        }
        if ($this->nid_front) {
            $data['nid_front'] = $this->handleImageUpload($this->nid_front, 'upgrade-document', $this->vendorDocument['nid_front']);
        }
        if ($this->nid_back) {
            $data['nid_back'] = $this->handleImageUpload($this->nid_back, 'upgrade-document', $this->vendorDocument['nid_back']);
        }

        $vd->update($data);

        $this->dispatch('refresh');
        $this->dispatch('success', 'Information Updated Successfully');
    }

    private function processImageStore($image, $targetStoreName)
    {
        //
        $targetPath = $this->upgrade == 'vendor' ? 'vendor-document' : 'reseller-document';
        if ($image) {
            $ext = $image->getClientOriginalExtension();
            $name = "$targetStoreName" . time() . ".$ext";
            // $filePath = $image->move(public_path($targetStorePath), $name);
            $image->storeAs($targetPath, $name, 'public');

            return $name;
        }
    }


    public function render()
    {
        return view('livewire.user.upgrade.vendor.edit')->layout('layouts.user.dash.userDash');
    }
}
