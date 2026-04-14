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
        Schema::create('reseller_has_documents', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('reseller_id')->nullable();
            $table->string('deatline', 100)->nullable();

            $table->string('nid', 100)->nullable();
            $table->string('nid_front', 100)->nullable();
            $table->string('nid_back', 100)->nullable();
            $table->string('shop_trade', 100)->nullable();
            $table->string('shop_trade_image', 100)->nullable();
            $table->string('shop_tin', 100)->nullable();
            $table->string('shop_tin_image', 100)->nullable();

            $table->string('payment_type', 100)->nullable();
            $table->string('payment_by', 100)->nullable();
            $table->string('payment_to', 100)->nullable();
            $table->string('holder_name', 100)->nullable();
            $table->string('swift_code', 100)->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_has_documents');
    }
};
