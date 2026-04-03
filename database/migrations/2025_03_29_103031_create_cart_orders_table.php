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
        Schema::create('cart_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_type', 100)->nullable()->default('user');
            $table->string('belongs_to', 100)->nullable();
            $table->string('belongs_to_type', 100)->nullable();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('quantity', 100)->nullable()->default('1');
            $table->string('price', 100)->nullable()->default('0');
            $table->string('size', 100)->nullable();
            $table->string('total', 100)->nullable();
            $table->string('buying_price', 100)->nullable();
            $table->string('status', 100)->nullable()->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_orders');
    }
};
