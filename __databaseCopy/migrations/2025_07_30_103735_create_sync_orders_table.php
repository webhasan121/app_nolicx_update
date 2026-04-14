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
        Schema::create('sync_orders', function (Blueprint $table) {
            $table->id();

            $table->string('user_order_id', 100)->nullable();
            $table->string('user_cart_order_id', 100)->nullable();
            $table->string('reseller_product_id', 100)->nullable();
            $table->string('reseller_order_id', 100)->nullable();
            $table->string('vendor_product_id', 100)->nullable();
            $table->string('user_id', 100)->nullable();
            $table->string('reseller_id', 100)->nullable();
            $table->string('vendor_id', 100)->nullable();
            $table->string('status', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_orders');
    }
};
