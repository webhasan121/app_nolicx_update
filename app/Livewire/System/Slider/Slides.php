<?php

namespace App\Livewire\System\Slider;

use App\HandleImageUpload;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Slider as sliderModel;
use App\Models\Slider_has_slide;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads as LivewireWithFileUploads;

#[layout('layouts.app')]
class Slides extends Component
{

    use LivewireWithFileUploads, HandleImageUpload;
    #[URL]
    public $id;

    public $slider, $slides = [], $image = [];


    public function mount()
    {
        $this->getData();
    }

    #[On('refresh')]
    public function getData()
    {
        $this->slider = sliderModel::find($this->id);
        $this->slides = Slider_has_slide::where(['slider_id' => $this->id])->get()->toArray();

        foreach ($this->slides as $key => $sl) {
            $this->image[$key]['image'] = $this->image[$key]['image'] ?? '';
        }
    }

    public function addNewSlides()
    {
        Slider_has_slide::create([
            'slider_id' => $this->id,
            'main_title' => '',
            'subtitle' => '',
            'desciprtion' => '',
            'image' => '',
            'action_url' => '/products',

        ]);
        $this->dispatch('refresh');
    }


    public function updateSlides($index, $id)
    {
        $ss = Slider_has_slide::find($id);

        $this->slides[$index]['image'] = $this->handleImageUpload($this->image[$index]['image'], 'slider', $this->slides[$index]['image']);

        $ss->update($this->slides[$index]);
        $this->dispatch('success', 'updated!');
        // $this->reset('image');
        $this->dispatch('refresh');
    }


    public function deleteSlides($id)
    {
        Slider_has_slide::destroy($id);
        $this->dispatch('refresh');
    }



    public function render()
    {
        return view('livewire.system.slider.slides');
    }
}
