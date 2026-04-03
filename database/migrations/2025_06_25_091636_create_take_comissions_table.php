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
        Schema::create('take_comissions', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id')->nullable(); // reseller or vendor
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('order_id')->nullable();
            $table->string('take_comission')->nullable(); // 20 tk as for 20% will be credeted form seller wallet
            $table->string('distribute_comission')->nullable(); // 12 Tk for 60%
            $table->string('store')->nullable(); // 20 - 12 = 8 Tk
            $table->string('return')->nullable(); // 100 - 20 = 80 tk will be added to seller wallet
            $table->string('previous_store')->nullable(); // 1000
            $table->string('current_store')->nullable(); // 1008 TK
            $table->string('buying_price')->nullable(); // 600
            $table->string('selling_price')->nullable(); // 700 
            $table->string('profit')->nullable(); // 100
            $table->string('comission_range')->nullable(); // 20%

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
        Schema::dropIfExists('take_comissions');
    }
};
