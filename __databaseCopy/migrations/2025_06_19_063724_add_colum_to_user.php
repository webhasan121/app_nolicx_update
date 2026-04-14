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
        Schema::table('users', function (Blueprint $table) {

            $table->boolean('vip')->nullable()->default(false);

            $table->string('country', 25)->nullable();
            $table->string('country_code', 25)->nullable();
            $table->string('zip', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('line1', 100)->nullable();
            $table->string('line2', 100)->nullable();
            $table->string('phone', 25)->nullable();

            $table->string('currency', 25)->nullable()->default('dollar');
            $table->string('currency_sing', 25)->nullable()->default('$');
            $table->string('language', 25)->nullable()->default('english');
            $table->string('site_language', 25)->nullable()->default('english');
            $table->boolean('kyc_status')->nullable()->default(true);
            $table->boolean('is_active')->nullable()->default(true);
            $table->string('metadata', 500)->nullable();
            $table->string('bio', 500)->nullable();
            $table->string('dob', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('vip');
            $table->dropColumnIfExists('country');
            $table->dropColumnIfExists('country_code');
            $table->dropColumnIfExists('zip');
            $table->dropColumnIfExists('state');
            $table->dropColumnIfExists('city');
            $table->dropColumnIfExists('line1');
            $table->dropColumnIfExists('line2');
            $table->dropColumnIfExists('phone');
            $table->dropColumnIfExists('currency');
            $table->dropColumnIfExists('currency_sing');
            $table->dropColumnIfExists('language');
            $table->dropColumnIfExists('site_language');
            $table->dropColumnIfExists('kyc_status');
            $table->dropColumnIfExists('is_active');
            $table->dropColumnIfExists('metadata');
            $table->dropColumnIfExists('bio');
            $table->dropColumnIfExists('dob');
        });
    }
};
