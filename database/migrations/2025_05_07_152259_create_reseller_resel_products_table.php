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
        Schema::create('reseller_resel_products', function (Blueprint $table) {
            $table->id();

            $table->string('user_id', 50)->nullable(); // reseller id
            $table->string('belongs_to', 50)->nullable(); // vndor id
            $table->string('product_id', 50)->nullable(); // reseller product id
            $table->string('parent_id', 50)->nullable(); // vendor product id

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_resel_products');
    }
};
