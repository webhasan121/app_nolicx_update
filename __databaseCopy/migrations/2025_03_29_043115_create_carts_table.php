<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_type')->nullable(); // user or reseller
            $table->string('belongs_to')->nullable(); // reseller or vendor id
            $table->string('belongs_to_type')->nullable(); // reseller or vendor
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('qty', 100)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('image', 191)->nullable();
            $table->string('price', 191)->nullable();
            $table->string('size', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
