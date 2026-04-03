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
        Schema::create('product_sales_indices', function (Blueprint $table) {
            $table->id();
            $table->string('product_id', 100)->nullable();
            $table->string('total_sales', 100)->nullable();
            $table->string('total_order', 100)->nullable();
            $table->string('user_type', 100)->nullable();
            $table->string('data2', 100)->nullable();
            $table->string('data3', 100)->nullable();
            $table->string('data4', 100)->nullable();
            $table->string('data5', 100)->nullable();
            $table->string('data6', 100)->nullable();
            $table->string('data7', 100)->nullable();
            $table->string('data8', 100)->nullable();
            $table->string('data9', 100)->nullable();
            $table->string('data10', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sales_indices');
    }
};
