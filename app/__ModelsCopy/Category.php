<?php

namespace App\Models;

use App\Livewire\System\Vendors\Vendor\Products;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // fillable data
    protected $fillable =
    [
        'name',
        'image',
        'user_id',
        'belongs_to', // parent category ID
        'description',
        'status',
        'slug',
    ];
    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getAll()
    {
        return self::whereNull('belongs_to')->orWhere('belongs_to', false)
            ->with(['children' => function ($query) {
                $query->orderBy('name');
            }, 'user'])
            ->get();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'belongs_to');
    }

    // default category for products
    public static function defaultCategory()
    {
        // Assuming 'default-category' is the slug for the default category
        if (self::where('slug', 'default-category')->count() === 0) {
            // or create a default category if needed
            self::create([
                'name' => 'Default Category',
                'slug' => 'default-category',
                'belongs_to' => null,
                'user_id' => 1, // Assuming user ID 1 is the admin or
            ]);
            return self::where('slug', 'default-category')->first();
        }
        // Return the default category if it exists
        return self::where('slug', 'default-category')->first();
    }

    // delete category
    public function deleteCategory()
    {
        // Check if the category has children before deleting
        if ($this->children->count() > 0) {
            $this->dispatch('error', 'Cannot delete category with subcategories.');
        };
        // default category cannot be deleted
        if ($this->slug === 'default-category') {
            $this->dispatch('error', 'Cannot delete the default category.');
        };
        // Check if the category has products
        if ($this->products->count() > 0) {
            // set the products category_id to default-category id
            $defaultCategory = self::where('slug', 'default-category')->first();
            if ($defaultCategory) {
                $this->products()->update(['category_id' => $defaultCategory->id]);
            } else {
                $this->dispatch('error', 'Default category not found. Cannot move products.');
            }
        };
        // Proceed with deletion
        $this->delete();
        return true;
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'belongs_to');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
