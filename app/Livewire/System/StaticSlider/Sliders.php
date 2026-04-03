<?php

namespace App\Livewire\System\StaticSlider;

use App\HandleImageUpload;
use App\Models\Static_slider_slides;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Layout;


#[layout('layouts.app')]
class Sliders extends Component
{
    use HandleImageUpload, WithFileUploads;

    #[URL]
    public $id;

    public $slides = [], $image, $url;

    public function mount()
    {
        $this->getData();
    }

    #[On('refresh')]
    public function getData()
    {
        $this->slides = Static_slider_slides::where('slider_id', $this->id)->get();
    }

    public function openSlidesCreateModal()
    {
        $this->dispatch('open-modal', 'slides-create-modal');
    }


    public function createSlides()
    {
        $this->validate(
            [
                'image' => ['required', 'max:1024']
            ]
        );
        try {

            Static_slider_slides::insert(
                [
                    'slider_id' => $this->id,
                    'image' => $this->handleImageUpload($this->image, 'static-slider', ''),
                    'action_url' => $this->url,
                ]
            );
            $this->reset('image');
            $this->getData();
            $this->dispatch('close-modal', 'slides-create-modal');
        } catch (\Throwable $th) {
            //throw $th;
            $this->reset('image');
            Log::error($th->getMessage());
            $this->dispatch('error', 'Have an error');
        }
    }


    public function deleteImage($id)
    {
        $image = Static_slider_slides::findOrFail($id);
        if ($image) {
            $image->delete();
            $this->getData();
            $this->dispatch('success', 'Deleted !');
        }
    }

    public function render()
    {
        return view('livewire.system.static-slider.sliders');
    }
}
