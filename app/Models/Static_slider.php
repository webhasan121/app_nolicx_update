<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Static_slider extends Model
{
    //
    protected $fillable = [
        'name',
        'status', // true, false
        'home',
        'product',
        'about',
        'product_details',
        'order',
        'categories_product',
        'grocery_item',
        'medicine_products',
        'food_items',
        'top_sales',
        'womens_item',
        'placement_top',
        'placement_middle',
        'placement_bottom',
        'slider_height',
    ];

    public $pages = [
        'home',
        'product',
        'about',
        'product_details',
        'order',
        'categories_product',
        'grocery_item',
        'medicine_products',
        'food_items',
        'top_sales',
        'womens_item',
    ];

    /**
     * scope
     */
    public function scopeHome($query)
    {
        return $query->where('home', true);
    }

    public function scopeProduct($query)
    {
        return $query->where('product', true);
    }

    public function scopeAbout($query)
    {
        return $query->where('about', true);
    }

    public function scopeProductDetails($query)
    {
        return $query->where('product_details', true);
    }

    public function scopeOrder($query)
    {
        return $query->where('order', true);
    }

    public function scopeCategoriesProduct($query)
    {
        return $query->where('categories_product', true);
    }

    public function scopeGroceryItem($query)
    {
        return $query->where('grocery_item', true);
    }

    public function scopeMedicineProducts($query)
    {
        return $query->where('medicine_products', true);
    }

    public function scopeFoodItems($query)
    {
        return $query->where('food_items', true);
    }

    public function scopeTopSales($query)
    {
        return $query->where('top_sales', true);
    }

    public function scopeWomensItem($query)
    {
        return $query->where('womens_item', true);
    }

    public function scopePlacementTop($query)
    {
        return $query->where('placement_top', true);
    }

    public function scopePlacementMiddle($query)
    {
        return $query->where('placement_middle', true);
    }

    public function scopePlacementBottom($query)
    {
        return $query->where('placement_bottom', true);
    }
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', false);
    }

    /**
     * scope for detirmined 
     */
    public function getIsHomeAttribute()
    {
        return (bool) $this->home;
    }

    public function getIsProductAttribute()
    {
        return (bool) $this->product;
    }

    public function getIsAboutAttribute()
    {
        return (bool) $this->about;
    }

    public function getIsProductDetailsAttribute()
    {
        return (bool) $this->product_details;
    }

    public function getIsOrderAttribute()
    {
        return (bool) $this->order;
    }

    public function getIsCategoriesProductAttribute()
    {
        return (bool) $this->categories_product;
    }

    public function getIsGroceryItemAttribute()
    {
        return (bool) $this->grocery_item;
    }

    public function getIsMedicineProductsAttribute()
    {
        return (bool) $this->medicine_products;
    }

    public function getIsFoodItemsAttribute()
    {
        return (bool) $this->food_items;
    }

    public function getIsTopSalesAttribute()
    {
        return (bool) $this->top_sales;
    }

    public function getIsWomensItemAttribute()
    {
        return (bool) $this->womens_item;
    }

    public function getIsPlacementTopAttribute()
    {
        return (bool) $this->placement_top;
    }

    public function getIsPlacementMiddleAttribute()
    {
        return (bool) $this->placement_middle;
    }

    public function getIsPlacementBottomAttribute()
    {
        return (bool) $this->placement_bottom;
    }
    public function getIsActiveAttribute()
    {
        return (bool) $this->status;
    }
    public function getIsDraftAttribute()
    {
        return (bool) $this->status;
    }

    /**
     * slideer has multiple slides
     */
    public function slides()
    {
        return $this->hasMany(Static_slider_slides::class, 'slider_id', 'id');
    }
}
