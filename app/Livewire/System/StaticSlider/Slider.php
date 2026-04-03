<?php

namespace App\Livewire\System\StaticSlider;

use Livewire\Component;
use App\HandleImageUpload;
use Livewire\Attributes\Layout;
use App\Models\Static_slider as sliderModel;
use App\Models\Static_slider_slides;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;

#[layout('layouts.app')]
class Slider extends Component
{
    use WithFileUploads, HandleImageUpload;
    #[URL]
    public $id;
    #[Reactive]
    public $index;
    public $slider = [];


    public function mount()
    {
        $this->getData();
    }

    public function getData()
    {
        $this->slider = sliderModel::findOrFail($this->id)->toArray();
    }

    public function updateStatusTrue(sliderModel $slider)
    {
        try {
            // sliderModel::query()->where(['placement' => $slider->placement])->update(['status' => false]);
            $slider->status = true;
            $slider->save();
            $this->getData();
        } catch (\Throwable $th) {
            // throw $th;
        }

        $this->dispatch('refresh');
    }
    public function updateStatusFalse(sliderModel $slider)
    {
        try {
            // sliderModel::query()->where(['placement' => $slider->placement])->update(['status' => false]);
            $slider->status = false;
            $slider->save();
            $this->getData();
        } catch (\Throwable $th) {
            throw $th;
        }
        $this->dispatch('refresh');
    }


    public function deleteSide()
    {
        // dd('deleted');
        try {
            sliderModel::destroy($this->id);
            Static_slider_slides::where('slider_id', $this->id)->delete();
            $this->dispatch('refresh');
            // $this->dispatch('success', 'Deleted !');
        } catch (\Throwable $th) {
            //throw $th;
            // $this->dispatch('error', 'Error !');
            Log::error($th->getMessage());
        }
    }


    public function openUpdateModal($id)
    {

        $this->dispatch('open-modal', 'open-slides-modal');
    }


    public function updateSlider()
    {
        $sl = sliderModel::find($this->id);
        $sl->update([
            'name' => $this->slider['name'],
            'status' => $this->slider['status'],
            'home' => $this->slider['home'],
            'product' => $this->slider['product'],
            'about' => $this->slider['about'],
            'order' => $this->slider['order'],
            'product_details' => $this->slider['product_details'],
            'categories_product' => $this->slider['categories_product'],
            'placement_top' => $this->slider['placement_top'],
            'placement_middle' => $this->slider['placement_middle'],
            'placement_bottom' => $this->slider['placement_bottom'],
        ]);

        $this->dispatch('success', 'Updated !');
        // dd($this->slider);
        $this->getData();
    }

    public function render()
    {
        return view('livewire.system.static-slider.slider');
    }
}
