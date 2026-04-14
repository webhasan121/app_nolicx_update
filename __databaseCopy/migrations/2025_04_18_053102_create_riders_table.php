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
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();

            $table->string('country')->nullable()->default('Bangladesh');
            $table->string('district')->nullable();
            $table->string('city', 155)->nullable();

            $table->string('nid')->nullable();
            $table->string('nid_photo_front')->nullable();
            $table->string('nid_photo_back')->nullable();
            $table->longText('fixed_address')->nullable();
            $table->longText('current_address')->nullable();

            $table->string('vehicle_type', 191)->nullable();
            $table->string('vehicle_number', 191)->nullable();
            $table->string('vehicle_model', 191)->nullable();
            $table->string('vehicle_color', 191)->nullable();

            $table->string('area_condition')->nullable();
            $table->string('targeted_area')->nullable();
            $table->string('status')->nullable();

            $table->string('fixed_amount', 100)->nullable()->default('500');
            $table->string('name', 100)->nullable()->default('Rider Name');
            $table->string('comission', 100)->nullable()->default('10');
            $table->boolean('is_rejected')->nullable()->default(false);
            $table->text('rejected_for')->nullable();

            $table->text('doc_1')->nullable();
            $table->text('doc_2')->nullable();
            $table->text('doc_3')->nullable();
            $table->text('doc_4')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riders');
    }
};
