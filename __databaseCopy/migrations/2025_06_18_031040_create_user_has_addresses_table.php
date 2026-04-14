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
        Schema::create('user_has_addresses', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('user_id');
            $table->string('label', 100)->nullable();
            $table->boolean('is_default')->default(false);

            $table->string('country', 100)->nullable();
            $table->string('country_code', 100)->nullable();
            $table->string('zip', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('line1', 100)->nullable();
            $table->string('line2', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('phone2', 100)->nullable();
            $table->string('phone3', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_addresses');
    }
};
