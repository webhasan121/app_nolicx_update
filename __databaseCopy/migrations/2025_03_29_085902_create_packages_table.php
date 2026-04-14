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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Product name
            $table->string('slug')->unique()->nullable(); // URL-friendly slug
            $table->integer('price')->nullable(); // Product price
            $table->integer('countdown')->nullable(); // Countdown timer (nullable)
            $table->integer('status')->nullable()->default(1);
            $table->integer('coin')->nullable();
            $table->integer('m_coin')->nullable();
            $table->longText('description')->nullable();
            $table->string('ref_owner_get_coin')->nullable();
            $table->string('owner_get_coin')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
