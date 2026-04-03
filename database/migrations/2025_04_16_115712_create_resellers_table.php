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
        Schema::create('resellers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id')->nullable();
            $table->string('shop_name_bn', 100)->nullable();
            $table->string('shop_name_en', 100)->nullable();
            $table->string('slug', 100)->nullable();
            $table->longText('description')->nullable();
            $table->text('logo')->nullable();
            $table->text('banner')->nullable();

            $table->string('address', 500)->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('upozila', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('zip', 100)->nullable();
            $table->string('road_no', 100)->nullable();
            $table->string('house_no', 100)->nullable();

            $table->boolean('is_rejected')->nullable();
            $table->string('rejected_for')->nullable();
            $table->text('system_get_comission')->nullable();
            $table->timestamp('information_update_date')->nullable();

            $table->string('status')->nullable()->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resellers');
    }
};
