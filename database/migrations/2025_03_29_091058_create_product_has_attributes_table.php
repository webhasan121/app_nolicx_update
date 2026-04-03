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
        Schema::create('product_has_attributes', function (Blueprint $table) {
            $table->id();
            // 'product_id',
            // 'name',
            // 'value',
            // 'stock',
            // 'price'
            $table->string('product_id')->nullable();
            $table->string('name')->nullable();
            $table->string('value')->nullable();
            $table->string('stock')->nullable();
            $table->string('price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_has_attributes');
    }
};
