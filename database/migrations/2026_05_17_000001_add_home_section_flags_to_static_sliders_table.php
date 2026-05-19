<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('static_sliders', function (Blueprint $table) {
            $table->boolean('grocery_item')->nullable()->after('categories_product');
            $table->boolean('medicine_products')->nullable()->after('grocery_item');
            $table->boolean('food_items')->nullable()->after('medicine_products');
            $table->boolean('top_sales')->nullable()->after('food_items');
            $table->boolean('womens_item')->nullable()->after('top_sales');
        });
    }

    public function down(): void
    {
        Schema::table('static_sliders', function (Blueprint $table) {
            $table->dropColumn([
                'grocery_item',
                'medicine_products',
                'food_items',
                'top_sales',
                'womens_item',
            ]);
        });
    }
};
