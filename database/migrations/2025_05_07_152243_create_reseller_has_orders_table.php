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
        Schema::create('reseller_has_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('user_id', 10)->nullable();
            $table->string('belongs_to', 10)->nullable();
            $table->string('quantity', 10)->nullable();
            $table->string('total', 10)->nullable();
            $table->string('status', 10)->nullable()->default('Pending');
            $table->string('district', 10)->nullable();
            $table->string('upozila', 10)->nullable();
            $table->string('location', 10)->nullable();
            $table->string('house_no', 10)->nullable();
            $table->string('road_no', 10)->nullable();
            $table->string('area_condition', 10)->nullable();
            $table->string('shipping', 10)->nullable();
            $table->string('delevery', 10)->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_has_orders');
    }
};
