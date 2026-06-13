<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_has_comments', function (Blueprint $table) {
            $table->json('images')->nullable()->after('image');
        });

        Schema::create('product_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('products_has_comment_id')->constrained('products_has_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['products_has_comment_id', 'user_id'], 'product_comment_likes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_comment_likes');

        Schema::table('products_has_comments', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
