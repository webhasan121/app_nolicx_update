<?php

namespace App\Livewire\System\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class Index extends Component
{
    // protected $listeners = ['$refresh'];
    public $collapse = false;
    public function render()
    {
        // Fetch categories from the database or any other source
        $categories = Category::getAll();
        return view('livewire.system.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function deleteCategory(Category $category)
    {

        // Check if the category exists
        // if (!$category) {
        //     $this->dispatch('error', 'Category not found.');
        //     return;
        // } else {
        //     $this->dispatch('confirm', 'Are you sure you want to delete this category?', function () use ($category) {
        //         // $this->deleteCategoryConfirmed($category);
        //         $category->deleteCategory();
        //         $this->dispatch('success', 'Category deleted successfully.');
        //     });
        //     return;
        // }

        // Check if the category has children before deleting
        if ($category->children->count() > 0) {
            $this->dispatch('error', 'Cannot delete category with subcategories.');
            return;
        }

        // default category cannot be deleted
        if ($category->slug === 'default-category') {
            $this->dispatch('error', 'Cannot delete the default category.');
            return;
        }

        // Check if the category has products
        if ($category->products->count() > 0) {
            // $this->dispatch('error', 'Cannot delete category with products.');
            // set the products category_id to default-cateogry id
            $defaultCategory = Category::where('slug', 'default-category')->first();
            if ($defaultCategory) {
                $category->products()->update(['category_id' => $defaultCategory->id]);
                $this->dispatch('success', 'Products moved to default category.');
            } else {
                $this->dispatch('error', 'Default category not found. Cannot move products.');
            }
            return;
        }

        // Proceed with deletion
        $category->delete();
        $this->dispatch('success', 'Category deleted successfully.');
    }

    #[On('added')]
    public function added()
    {
        $this->dispatch('refresh');
        $this->dispatch('close-modal', 'category_create');
    }
}
