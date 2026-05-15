<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('notices', 'order_id')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->unsignedBigInteger('order_id')->nullable()->after('body')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notices', 'order_id')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->dropIndex(['order_id']);
                $table->dropColumn('order_id');
            });
        }
    }
};
