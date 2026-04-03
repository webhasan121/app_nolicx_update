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
        Schema::create('navigations_has_links', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->bigInteger('navigations_id')->nullable();
            $table->string('url')->nullable()->default('/');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigations_has_links');
    }
};
