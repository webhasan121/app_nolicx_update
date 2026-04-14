<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('static_sliders', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('home')->nullable();
            $table->boolean('product')->nullable();
            $table->boolean('about')->nullable();
            $table->boolean('product_details')->nullable();
            $table->boolean('order')->nullable();
            $table->boolean('categories_product')->nullable();
            $table->boolean('placement_top')->nullable();
            $table->boolean('placement_middle')->nullable();
            $table->boolean('placement_bottom')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_sliders');
    }
};
