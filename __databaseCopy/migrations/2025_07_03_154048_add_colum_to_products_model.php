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

            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();

            $table->string('meta_title', 500)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('keyword', 255)->nullable();
            $table->string('meta_tags', 500)->nullable();
            $table->string('meta_thumbnail', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumnIfExists('country');
            $table->dropColumnIfExists('state');
            $table->dropColumnIfExists('meta_title');
            $table->dropColumnIfExists('meta_description');
            $table->dropColumnIfExists('keyword');
            $table->dropColumnIfExists('meta_tags');
            $table->dropColumnIfExists('meta_thumbnail');
        });
    }
};
