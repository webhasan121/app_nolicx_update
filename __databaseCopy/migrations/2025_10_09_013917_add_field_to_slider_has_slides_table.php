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
        Schema::table('slider_has_slides', function (Blueprint $table) {
            $table->string('title_color')->nullable();
            $table->string('des_color')->nullable();
            $table->string('action_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slider_has_slides', function (Blueprint $table) {
            $table->dropColumn(['title_color', 'des_color', 'action_text']);
        });
    }
};
