<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('static_sliders', function (Blueprint $table) {
            $table->unsignedInteger('slider_height')->nullable()->after('placement_bottom');
        });
    }

    public function down(): void
    {
        Schema::table('static_sliders', function (Blueprint $table) {
            $table->dropColumn('slider_height');
        });
    }
};
