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
        Schema::create('vips', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('nid_front')->nullable();
            $table->string('nid_back')->nullable();
            $table->string('nid')->nullable();
            $table->string('payment_by')->nullable();
            $table->string('trx')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->integer('status')->nullable()->default(0);
            $table->text('valid_till')->nullable();
            $table->text('valid_from')->nullable();
            $table->string('task_type')->nullable()->default('daily');
            $table->string('reference', 100)->nullable()->default('REF101U');
            $table->string('comission', 100)->nullable()->default(100);
            $table->string('refer')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vips');
    }
};
