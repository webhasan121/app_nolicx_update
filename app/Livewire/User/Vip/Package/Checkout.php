<?php

namespace App\Livewire\User\Vip\Package;

use App\HandleImageUpload;
use App\Models\Packages;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\vip;
use Illuminate\Support\Facades\Storage;

use function Illuminate\Log\log;

#[layout('layouts.user.dash.userDash')]
class Checkout extends Component
{
    use WithFileUploads, HandleImageUpload;
    #[URL]
    public $id;
    public $package, $ownerPackage;

    #[validate('required')]
    public $payment_by, $trx, $name, $phone, $task_type, $nid, $nid_front, $nid_back;

    public function mount()
    {
        $this->package = Packages::find($this->id);
        $this->ownerPackage = 1;
    }

    public function purchase()
    {
        $validate = $this->validate();
        if (Auth::user()->vipPurchase?->package) {
            // if already purchase one package, can't purchase another.
            $this->dispatch('warning', 'You have already active package');
        } else {

            // check purchase package already purchase or not
            if (vip::where(['user_id' => Auth::id(), 'package_id' => $this->package?->id])->first()) {
                $this->dispatch('warning', 'You already purchase this packages.');
            } else {

                try {
                    //code...
                    /**
                     * else, process the purchase
                     * store the request to database with status 0
                     */
                    $validate['status'] = 0;
                    $validate['name'] = $this->name;
                    $validate['phone'] = $this->phone;
                    $validate['payment_by'] = $this->payment_by;
                    $validate['trx'] = $this->trx;
                    $validate['user_id'] = Auth::id();
                    $validate['package_id'] = $this->package?->id;

                    /**
                     * process the image upload
                     * image saved to the $validate array
                     */

                    $validate['nid_front'] = $this->handleImageUpload($this->nid_front, 'vips', null);
                    $validate['nid_back'] = $this->handleImageUpload($this->nid_back, 'vip', null);
                    $validate['reference'] = Auth::user()->reference;
                    $validate['comission'] = $this->package?->ref_owner_get_coin;
                    $validate['refer'] = Auth::user()->getReffOwner?->owner?->id;
                    $validate['task_type'] = $this->task_type;

                    // dd($validate);
                    $userPackage = vip::create($validate);

                    /**
                     * if data not created
                     * delete the related image from storage
                     */
                    if (!$userPackage) {
                        if (file_exists(public_path('storage/' . $this->nid_front))) {
                            Storage::disk('public')->delete($this->nid_front);
                        }
                        if (file_exists(public_path('storage/' . $this->nid_back))) {
                            Storage::disk('public')->delete($this->nid_back);
                        }
                    }


                    /**
                     * redirect to user dash
                     */
                    $this->redirectIntended(route('user.vip.index'), true);
                } catch (\Throwable $th) {
                    //throw $th;
                    log($th);
                    $this->dispatch('error', 'Have an error while purchasing, try again later.');
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.user.vip.package.checkout');
    }
}
