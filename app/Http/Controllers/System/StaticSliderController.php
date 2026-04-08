<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Static_slider;
use App\Models\Static_slider_slides;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaticSliderController extends Controller
{
    public function indexReact(): Response
    {
        $sliders = Static_slider::query()->orderBy('id', 'desc')->get();

        return Inertia::render('Auth/system/static-slider/index', [
            'slider' => $sliders->map(fn (Static_slider $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'status' => (bool) $item->status,
                'home' => (bool) $item->home,
                'about' => (bool) $item->about,
                'order' => (bool) $item->order,
                'product_details' => (bool) $item->product_details,
                'categories_product' => (bool) $item->categories_product,
                'placement_top' => (bool) $item->placement_top,
                'placement_middle' => (bool) $item->placement_middle,
                'placement_bottom' => (bool) $item->placement_bottom,
            ])->values()->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sliderName' => ['required'],
        ]);

        Static_slider::create([
            'name' => $validated['sliderName'],
            'status' => $request->boolean('status'),
            'home' => $request->boolean('home'),
            'about' => $request->boolean('about'),
            'order' => $request->boolean('order'),
            'product_details' => $request->boolean('product_details'),
            'categories_product' => $request->boolean('categories_product'),
            'placement_top' => $request->boolean('top'),
            'placement_middle' => $request->boolean('middle'),
            'placement_bottom' => $request->boolean('bottom'),
        ]);

        return redirect()->route('system.static-slider.index')->with('success', 'Added !');
    }

    public function update(Request $request, Static_slider $slider): RedirectResponse
    {
        $slider->update([
            'name' => $request->input('name'),
            'status' => $request->boolean('status'),
            'home' => $request->boolean('home'),
            'about' => $request->boolean('about'),
            'order' => $request->boolean('order'),
            'product_details' => $request->boolean('product_details'),
            'categories_product' => $request->boolean('categories_product'),
            'placement_top' => $request->boolean('placement_top'),
            'placement_middle' => $request->boolean('placement_middle'),
            'placement_bottom' => $request->boolean('placement_bottom'),
        ]);

        return redirect()->route('system.static-slider.index')->with('success', 'Updated !');
    }

    public function updateStatus(Request $request, Static_slider $slider): RedirectResponse
    {
        $slider->status = $request->boolean('status');
        $slider->save();

        return redirect()->route('system.static-slider.index')->with('success', 'Updated !');
    }

    public function destroy(Static_slider $slider): RedirectResponse
    {
        Static_slider_slides::where('slider_id', $slider->id)->delete();
        $slider->delete();

        return redirect()->route('system.static-slider.index')->with('success', 'Deleted !');
    }

    public function slidesReact(int $id): Response|RedirectResponse
    {
        $slider = Static_slider::find($id);

        if (!$slider) {
            return redirect()->route('system.static-slider.index');
        }

        return Inertia::render('Auth/system/static-slider/slides', [
            'id' => $slider->id,
            'slides' => Static_slider_slides::where('slider_id', $slider->id)->get()->map(fn (Static_slider_slides $item) => [
                'id' => $item->id,
                'image' => $item->image,
                'action_url' => $item->action_url,
            ])->values()->all(),
        ]);
    }

    public function storeSlide(Request $request, Static_slider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'max:1024'],
            'url' => ['nullable'],
        ]);

        Static_slider_slides::create([
            'slider_id' => $slider->id,
            'image' => $request->hasFile('image') ? $this->handleImageUpload($request->file('image'), 'static-slider', '') : '',
            'action_url' => $validated['url'] ?? null,
        ]);

        return redirect()->route('system.static-slider.slides', ['id' => $slider->id])->with('success', 'Saved !');
    }

    public function destroySlide(Static_slider_slides $slide): RedirectResponse
    {
        $sliderId = $slide->slider_id;
        $slide->delete();

        return redirect()->route('system.static-slider.slides', ['id' => $sliderId])->with('success', 'Deleted !');
    }
}
