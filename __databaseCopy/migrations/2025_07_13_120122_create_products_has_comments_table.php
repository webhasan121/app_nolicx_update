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
        Schema::defaultStringLength(191);
        Schema::create('products_has_comments', function (Blueprint $table) {
            $table->id();
            $table->engine = 'InnoDB';
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('is_verified_user')->nullable();
            $table->longText('comments')->nullable();
            $table->text('image')->nullable();
            $table->text('review')->nullable();
            $table->boolean('approved')->nullable()->default(false);
            $table->integer('like')->nullable()->default(0);
            $table->integer('unlike')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_has_comments');
    }
};
