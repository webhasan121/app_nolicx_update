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
        Schema::table('products', function (Blueprint $table) {
            $table->string('vc', 100)->nullable()->default(0); // view count
            $table->string('brand', 100)->nullable(); // for product brand

            $table->boolean('cod')->nullable()->default(false); // is cash on delevery accept or not
            $table->boolean('courier')->nullable()->default(true); // is couries delevery accept or not
            $table->boolean('hand')->nullable()->default(true); // is hand-to-hand delevery accept or not

            $table->string('shipping_in_dhaka')->nullable()->default(80); // delevery in dhaka
            $table->string('shipping_out_dhaka')->nullable()->default(120); // delevery other destricts
            $table->string('shipping_note')->nullable(); // shipping note

            $table->json('badge')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('accept_cuppon')->nullable()->default(false);
            //  $table->string('name', 100)->nullable()->default('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
