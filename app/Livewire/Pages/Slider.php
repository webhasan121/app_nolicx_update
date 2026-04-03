<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Slider_has_slide;
use App\Models\Slider as sliderModel;

class Slider extends Component
{
    public function render()
    {
        $slider = sliderModel::query()->where(['status' => true])->whereNot('placement', '=', 'apps')->orderBy('id', 'desc')->get('id')->pluck('id');
        // $slider = Slider::query()->where(['status' => true])->orderBy('id', 'desc')->get('id')->pluck('id');
        $slides = Slider_has_slide::query()->whereIn('slider_id', $slider)->get();;
        // dd($slides);
        return view('livewire.pages.slider', compact('slides'));
    }
}
