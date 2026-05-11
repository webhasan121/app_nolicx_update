<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'year')) {
                $table->unsignedSmallInteger('year')->nullable()->after('coin');
            }

            if (!Schema::hasColumn('stores', 'month')) {
                $table->unsignedTinyInteger('month')->nullable()->after('year');
            }

            if (!Schema::hasColumn('stores', 'total_balance')) {
                $table->decimal('total_balance', 15, 8)->default(0)->after('month');
            }

            if (!Schema::hasColumn('stores', 'current_balance')) {
                $table->decimal('current_balance', 15, 8)->default(0)->after('total_balance');
            }

            if (!Schema::hasColumn('stores', 'distribute_balance')) {
                $table->decimal('distribute_balance', 15, 8)->default(0)->after('current_balance');
            }

            if (!Schema::hasColumn('stores', 'generate')) {
                $table->boolean('generate')->default(false)->after('distribute_balance');
            }
        });

        Schema::table('distribute_comissions', function (Blueprint $table) {
            if (!Schema::hasColumn('distribute_comissions', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'current_level_id')) {
                $table->foreignId('current_level_id')->nullable()->default(1)->after('vip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'current_level_id')) {
                $table->dropColumn('current_level_id');
            }
        });

        Schema::table('distribute_comissions', function (Blueprint $table) {
            if (Schema::hasColumn('distribute_comissions', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });

        Schema::table('stores', function (Blueprint $table) {
            foreach (['generate', 'distribute_balance', 'current_balance', 'total_balance', 'month', 'year'] as $column) {
                if (Schema::hasColumn('stores', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
