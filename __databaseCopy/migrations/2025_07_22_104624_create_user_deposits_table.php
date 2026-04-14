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
        Schema::create('user_deposits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('amount', 100)->nullable();
            $table->string('paymentMethod', 100)->nullable();
            $table->string('receiverAccountNumber', 100)->nullable();
            $table->string('senderAccountNumber', 100)->nullable();
            $table->string('senderName', 100)->nullable();
            $table->string('transactionId', 100)->nullable();

            $table->boolean('confirmed')->nullable()->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_deposits');
    }
};
