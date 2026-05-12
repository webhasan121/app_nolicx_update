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
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'developer_percentage')) {
                $table->decimal('developer_percentage', 8, 2)->default(0)->after('generate');
            }

            if (!Schema::hasColumn('stores', 'management_percentage')) {
                $table->decimal('management_percentage', 8, 2)->default(0)->after('developer_percentage');
            }

            if (!Schema::hasColumn('stores', 'management_team_percentage')) {
                $table->decimal('management_team_percentage', 8, 2)->default(0)->after('management_percentage');
            }

            if (!Schema::hasColumn('stores', 'star_system_percentage')) {
                $table->decimal('star_system_percentage', 8, 2)->default(0)->after('management_team_percentage');
            }

            if (!Schema::hasColumn('stores', 'total_share')) {
                $table->decimal('total_share', 8, 2)->default(0)->after('star_system_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            foreach ([
                'total_share',
                'star_system_percentage',
                'management_team_percentage',
                'management_percentage',
                'developer_percentage',
            ] as $column) {
                if (Schema::hasColumn('stores', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
