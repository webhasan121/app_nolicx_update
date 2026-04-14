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
        Schema::create('reseller_resell_profits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from')->nullable();
            $table->bigInteger('to')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('order_id')->nullable();


            $table->string('profit')->nullable();
            $table->string('buy')->nullable();
            $table->string('sel')->nullable();

            $table->boolean('confirmed')->nullable()->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_resell_profits');
    }
};
