<?php

namespace App\Livewire\System\StaticSlider;

use Livewire\Component;
use App\HandleImageUpload;
use Livewire\Attributes\Layout;
use App\Models\Static_slider as sliderModel;
use App\Models\Static_slider_slides;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;

#[layout('layouts.app')]
class Index extends Component
{
    use HandleImageUpload, WithFileUploads;

    #[URL]
    public $sliderId;

    public $slider;
    #[validate('required')]
    public $sliderName;
    public $sliderImage, $sliderPlacement = 'web', $status = false, $sler = '', $slides = [], $ss, $updateable = [];
    public $home, $product, $order, $about, $product_details, $categories_product, $top, $middle, $bottom, $image, $url;

    public function mount()
    {
        $this->getData();
    }

    #[On('refresh')]
    public function getData()
    {
        $this->slider = sliderModel::orderBy('id', 'desc')->get()->toArray();
        // dd($this->slider);
    }



    public function createNewSlider()
    {
        $this->validate();
        try {


            DB::transaction(function () {

                sliderModel::create([
                    'name' => $this->sliderName,
                    'placement' => $this->sliderPlacement,
                    'status' => $this->status,
                    'home' => $this->home,
                    'product' => $this->product,
                    'about' => $this->about,
                    'order' => $this->order,
                    'product_details' => $this->product_details,
                    'categories_product' => $this->categories_product,
                    'placement_top' => $this->top,
                    'placement_middle' => $this->middle,
                    'placement_bottom' => $this->bottom,
                ]);
            });
            $this->getData();
            $this->dispatch('close-modal', 'open-slider-modal');
        } catch (\Throwable $th) {
            // dd($th);
            Log::error($th);
        }
    }


   

    public function render()
    {
        return view('livewire.system.static-slider.index');
    }
}
