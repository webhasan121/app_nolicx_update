<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('stores', 'total_share')) {
            DB::statement('ALTER TABLE stores MODIFY total_share DECIMAL(15,8) NOT NULL DEFAULT 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('stores', 'total_share')) {
            DB::statement('ALTER TABLE stores MODIFY total_share DECIMAL(8,2) NOT NULL DEFAULT 0');
        }
    }
};
