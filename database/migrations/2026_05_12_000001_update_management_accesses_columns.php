<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('management_accesses')) {
            Schema::create('management_accesses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('applied_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('commission', 10, 2)->nullable();
                $table->text('message')->nullable();
                $table->boolean('status')->nullable();
                $table->foreignId('response_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique('applied_id');
            });

            return;
        }

        Schema::table('management_accesses', function (Blueprint $table) {
            if (!Schema::hasColumn('management_accesses', 'applied_id')) {
                $table->foreignId('applied_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('management_accesses', 'commission')) {
                $table->decimal('commission', 10, 2)->nullable()->after('applied_id');
            }

            if (!Schema::hasColumn('management_accesses', 'message')) {
                $table->text('message')->nullable()->after('commission');
            }

            if (!Schema::hasColumn('management_accesses', 'status')) {
                $table->boolean('status')->nullable()->after('message');
            }

            if (!Schema::hasColumn('management_accesses', 'response_by')) {
                $table->foreignId('response_by')->nullable()->after('status');
            }
        });

        if (Schema::hasColumn('management_accesses', 'user_id') && Schema::hasColumn('management_accesses', 'applied_id')) {
            DB::table('management_accesses')
                ->whereNull('applied_id')
                ->update(['applied_id' => DB::raw('user_id')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep existing management data intact.
    }
};
