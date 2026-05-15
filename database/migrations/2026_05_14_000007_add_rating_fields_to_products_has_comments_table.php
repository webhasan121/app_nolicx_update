<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_has_comments', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->after('comments');
            $table->unsignedBigInteger('order_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('cart_order_id')->nullable()->after('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('products_has_comments', function (Blueprint $table) {
            $table->dropColumn(['rating', 'order_id', 'cart_order_id']);
        });
    }
};
