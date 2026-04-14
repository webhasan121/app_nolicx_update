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
        Schema::create('static_slider_slides', function (Blueprint $table) {
            $table->id();
            $table->integer('slider_id');
            $table->text('image')->nullable();
            $table->string('status')->nullable()->default(1);
            $table->string('action_type')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_target')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_slider_slides');
    }
};
