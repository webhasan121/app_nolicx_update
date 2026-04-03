<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_type')->nullable();
            $table->string('belongs_to')->nullable();
            $table->string('belongs_to_type')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->string('size')->nullable();
            $table->string('name')->nullable();
            $table->string('price')->nullable();
            $table->string('quantity')->nullable();
            $table->string('location')->nullable();
            $table->string('number')->nullable();
            $table->string('house_no')->nullable();
            $table->string('road_no')->nullable();
            $table->string('total')->nullable();
            $table->string('shipping')->nullable();
            $table->string('buying_price')->nullable();
            $table->string('area_condition')->nullable();
            $table->string('district')->nullable();
            $table->string('upozila')->nullable();
            $table->string('delevery')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
