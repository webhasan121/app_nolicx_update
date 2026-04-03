<?php

namespace App\Livewire\User\Upgrade\Rider;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\rider;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use App\HandleImageUpload;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\This;

#[layout("layouts.user.dash.userDash")]
class Edit extends Component
{
    use WithFileUploads, HandleImageUpload;

    #[URL]
    public $id;
    private $data;
    public $rider, $nid_photo_front, $nid_photo_back;

    public function mount()
    {
        $this->data = rider::find($this->id);
        $this->rider = $this->data->toArray();
        // dd($this->rider);

        if ($this->rider['status'] != 'Pending') {
            return redirect()->back()->with('error', 'Unable to edit or update');
        }
    }




    public function store()
    {
        // $updatedData = [
        //     'phone' => $this->rider['phone'],
        //     'email' => $this->rider['email'],
        //     'nid' => $this->rider['nid'],
        //     'fixed_address' => $this->rider['fixed_address'],
        //     'current_address' => $this->rider['current_address'],
        //     'area_condition' => $this->rider['area_condition'],
        //     'targeted_area' => $this->rider['targeted_area'],
        // ];
        $this->rider['nid_photo_front'] = $this->handleImageUpload($this->nid_photo_front, 'rider-document', $this->rider['nid_photo_front']);
        $this->rider['nid_photo_back'] = $this->handleImageUpload($this->nid_photo_back, 'rider-document', $this->rider['nid_photo_back']);
        // if (isset($this->nid_photo_front)) {
        //     $this->rider['nid_photo_front'] = $this->processImageStore($this->nid_photo_front, 'rider-nid-front-');
        // } else {
        //     $this->rider['nid_photo_front'] = $this->data->nid_photo_front ?? null;
        // }
        // if (isset($this->nid_photo_back)) {
        //     $this->rider['nid_photo_back'] = $this->processImageStore($this->nid_photo_back, 'rider-nid-back-');
        // } else {
        //     $this->rider['nid_photo_back'] = $this->data->nid_photo_back ?? null; // $this->data->nid_photo_back;
        // }

        // dd($this->rider);
        rider::find($this->id)->update($this->rider);
        $this->dispatch('success', 'Information Updated Successfully');
        $this->reset(['nid_photo_front', 'nid_photo_back']);
        // Session::flash('success', "Information updated !");
    }

    private function processImageStore($image, $targetStoreName)
    {
        //
        $targetPath = 'rider-document';
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
        return view('livewire..user.upgrade.rider.edit');
    }
}
