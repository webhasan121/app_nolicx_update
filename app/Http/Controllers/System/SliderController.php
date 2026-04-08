<?php

namespace App\Http\Controllers\System;

use App\HandleImageUpload;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Slider_has_slide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SliderController extends Controller
{
    use HandleImageUpload;

    public function indexReact(Request $request): Response
    {
        $nav = $request->query('nav', 'web');
        $slider = Slider::where(['placement' => $nav])->with('slides')->orderBy('id', 'desc')->get();
        $editId = $request->integer('edit');
        $updateable = $editId ? Slider::find($editId) : null;

        return Inertia::render('Auth/system/slider/index', [
            'nav' => $nav,
            'slider' => $slider->map(fn (Slider $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'placement' => $item->placement,
                'status' => (bool) $item->status,
                'slides_count' => $item->slides->count(),
            ])->values()->all(),
            'updateable' => $updateable ? [
                'id' => $updateable->id,
                'name' => $updateable->name,
                'placement' => $updateable->placement,
                'status' => (bool) $updateable->status,
            ] : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sliderName' => 'required',
            'sliderPlacement' => 'nullable',
            'status' => 'nullable|boolean',
            'sliderImage' => 'nullable',
            'background_color' => 'nullable',
            'nav' => 'nullable',
        ]);

        Slider::create([
            'name' => $validated['sliderName'],
            'placement' => $validated['sliderPlacement'] ?? 'web',
            'image' => $this->handleImageUpload($request->file('sliderImage'), 'slider', null),
            'backgrond_color' => $validated['background_color'] ?? null,
            'status' => $validated['status'] ?? true,
        ]);

        return redirect()->route('system.slider.index', [
            'nav' => $validated['nav'] ?? ($validated['sliderPlacement'] ?? 'web'),
        ])->with('success', 'Slider added successfully.');
    }

    public function updateStatus(Request $request, Slider $slider): RedirectResponse
    {
        $slider->status = $request->boolean('status');
        $slider->save();

        return redirect()->route('system.slider.index', [
            'nav' => $request->query('nav', $slider->placement),
        ])->with('success', 'Slider status updated successfully.');
    }

    public function destroy(Request $request, Slider $slider): RedirectResponse
    {
        $slider->delete();

        return redirect()->route('system.slider.index', [
            'nav' => $request->query('nav', 'web'),
        ])->with('success', 'Slider deleted successfully.');
    }

    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable',
            'placement' => 'nullable',
            'nav' => 'nullable',
        ]);

        $slider->update([
            'name' => $validated['name'],
            'placement' => $validated['placement'],
            'status' => true,
        ]);

        return redirect()->route('system.slider.index', [
            'nav' => $validated['nav'] ?? $validated['placement'] ?? 'web',
            'edit' => $slider->id,
        ])->with('success', 'Slider updated successfully.');
    }

    public function slidesReact(Request $request): Response|RedirectResponse
    {
        $id = $request->integer('id');
        $slider = Slider::find($id);

        if (!$slider) {
            return redirect()->route('system.slider.index');
        }

        $slides = Slider_has_slide::where(['slider_id' => $id])->get();

        return Inertia::render('Auth/system/slider/slides', [
            'slider' => [
                'id' => $slider->id,
                'name' => $slider->name,
            ],
            'slides' => $slides->map(fn (Slider_has_slide $item) => [
                'id' => $item->id,
                'slider_id' => $item->slider_id,
                'main_title' => $item->main_title,
                'subtitle' => $item->subtitle,
                'description' => $item->description,
                'image' => $item->image,
                'status' => $item->status,
                'action_type' => $item->action_type,
                'action_url' => $item->action_url,
                'action_target' => $item->action_target,
                'title_color' => $item->title_color,
                'des_color' => $item->des_color,
                'action_text' => $item->action_text,
            ])->values()->all(),
        ]);
    }

    public function addSlide(Slider $slider): RedirectResponse
    {
        Slider_has_slide::create([
            'slider_id' => $slider->id,
            'main_title' => '',
            'subtitle' => '',
            'desciprtion' => '',
            'image' => '',
            'action_url' => '/products',
        ]);

        return redirect()->route('system.slider.slides', [
            'id' => $slider->id,
        ])->with('success', 'Slide added successfully.');
    }

    public function updateSlide(Request $request, Slider_has_slide $slide): RedirectResponse
    {
        $validated = $request->validate([
            'main_title' => 'nullable',
            'description' => 'nullable',
            'action_text' => 'nullable',
            'action_url' => 'nullable',
            'action_target' => 'nullable',
            'title_color' => 'nullable',
            'des_color' => 'nullable',
            'image' => 'nullable',
        ]);

        $slide->update([
            'main_title' => $validated['main_title'] ?? '',
            'description' => $validated['description'] ?? '',
            'action_text' => $validated['action_text'] ?? '',
            'action_url' => $validated['action_url'] ?? '',
            'action_target' => $validated['action_target'] ?? '',
            'title_color' => $validated['title_color'] ?? '',
            'des_color' => $validated['des_color'] ?? '',
            'image' => $this->handleImageUpload($request->file('image'), 'slider', $slide->image),
        ]);

        return redirect()->route('system.slider.slides', [
            'id' => $slide->slider_id,
        ])->with('success', 'updated!');
    }

    public function destroySlide(Slider_has_slide $slide): RedirectResponse
    {
        $sliderId = $slide->slider_id;
        $slide->delete();

        return redirect()->route('system.slider.slides', [
            'id' => $sliderId,
        ])->with('success', 'Slide deleted successfully.');
    }
}
