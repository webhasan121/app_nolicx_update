<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\FooterLayout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FooterBuilderController extends Controller
{
    public function indexReact(): Response
    {
        $layout = [
            'sections' => [],
        ];

        $footer = FooterLayout::where('name', 'default')->first();
        if ($footer) {
            $layout = json_decode($footer->layout, true) ?: $layout;
        }

        return Inertia::render('Auth/system/footer-builder/index', [
            'layoutData' => $layout,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $layout = $request->input('layout', [
            'sections' => [],
        ]);

        FooterLayout::updateOrCreate(
            ['name' => 'default'],
            ['layout' => json_encode($layout)]
        );

        return redirect()->back()->with('success', 'Footer layout saved!');
    }
}
