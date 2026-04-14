<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class CategoryIndexController extends Controller
{
    public function index()
    {
        return Inertia::render('Products/CategoriesIndex', [
            'categories' => Category::getAll(),
        ]);
    }
}
