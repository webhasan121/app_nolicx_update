<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('notices', 'target_user_id')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->foreignId('target_user_id')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('orders')) {
            DB::table('notices')
                ->whereNotNull('order_id')
                ->whereNull('target_user_id')
                ->orderBy('id')
                ->chunkById(500, function ($notices) {
                    foreach ($notices as $notice) {
                        $userId = DB::table('orders')
                            ->where('id', $notice->order_id)
                            ->value('user_id');

                        if ($userId) {
                            DB::table('notices')
                                ->where('id', $notice->id)
                                ->update(['target_user_id' => $userId]);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notices', 'target_user_id')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->dropConstrainedForeignId('target_user_id');
            });
        }
    }
};
