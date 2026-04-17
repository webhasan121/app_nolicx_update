<?php

namespace App\Http\Controllers;

use App\Models\page_settings;
use Inertia\Inertia;
use Inertia\Response;

class WebPageController extends Controller
{
    public function show(string $slug): Response
    {
        $page = page_settings::query()->where('slug', $slug)->first();
        $otherPages = page_settings::query()->get();

        return Inertia::render('Web/Page', [
            'slug' => $slug,
            'page' => $page,
            'otherPages' => $otherPages,
        ]);
    }
}
