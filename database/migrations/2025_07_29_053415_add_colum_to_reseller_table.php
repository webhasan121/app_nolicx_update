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
        Schema::table('resellers', function (Blueprint $table) {
            $table->boolean('allow_max_product_upload')->nullable()->default(true);
            $table->string('max_product_upload', 100)->nullable()->default('5'); // a vendor can upload by default 5 products


            $table->boolean('prevent_daily_product_upload')->nullable()->default(true);
            $table->string('max_product_upload_daily', 100)->nullable()->default('5'); // a vendor can upload by default 5 products for reseller

            $table->boolean('prevent_monthly_product_upload')->nullable()->default(false);
            $table->string('max_product_upload_monthly', 100)->nullable()->default('5'); // a vendor can upload by default 5 products for reseller

            // for order 
            $table->boolean('allow_max_resell_product')->nullable()->default(true);
            $table->string('max_resell_product', 100)->nullable()->default('5'); // a reseller can resell by default 5 products

            $table->boolean('prevent_daily_resell_product')->nullable()->default(true);
            $table->string('max_resell_product_daily', 100)->nullable()->default('5'); // a reseller can resell by default 5 products for reseller

            $table->boolean('prevent_monthly_resell_product')->nullable()->default(false);
            $table->string('max_resell_product_monthly', 100)->nullable()->default('5'); // a reseller can resell by default 5 products for reseller

            $table->boolean('can_resell_products')->nullable()->default(true);
            $table->boolean('can_accept_order')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn([
                'allow_max_product_upload',
                'max_product_upload',
                'prevent_daily_product_upload',
                'max_product_upload_daily',
                'prevent_monthly_product_upload',
                'max_product_upload_monthly',
                'can_resell_products',
                'max_resell_product',
                'can_accept_order'
            ]);
        });
    }
};
