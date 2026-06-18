<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribute_comissions', function (Blueprint $table) {
            if (!Schema::hasColumn('distribute_comissions', 'wallet_credited_at')) {
                $table->timestamp('wallet_credited_at')->nullable()->after('confirmed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('distribute_comissions', function (Blueprint $table) {
            if (Schema::hasColumn('distribute_comissions', 'wallet_credited_at')) {
                $table->dropColumn('wallet_credited_at');
            }
        });
    }
};
