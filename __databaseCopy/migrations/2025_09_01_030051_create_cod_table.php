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
        Schema::create('cod', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable();
            $table->bigInteger('rider_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('seller_id')->nullable(); // reseller / vendor ID
            $table->string('seller_type')->nullable(); // reseller / vendor
            $table->string('payment_method')->default('cash');

            $table->decimal('amount', 10, 2)->default(0.00); // Amount to be collected rider + reseller + vendor + system
            $table->decimal('paid_amount', 10, 2)->default(0.00); // Amount already paid
            $table->decimal('due_amount', 10, 2)->default(0.00); // Amount still due
            $table->decimal('total_amount', 10, 2)->default(0.00); // Total amount including all commissions

            $table->decimal('rider_amount', 10, 2)->default(0.00); // Amount to be paid to the rider
            $table->decimal('reseller_amount', 10, 2)->default(0.00); // Amount to be paid to the reseller
            $table->decimal('vendor_amount', 10, 2)->default(0.00); // Amount to be paid to the vendor
            $table->decimal('system_amount', 10, 2)->default(0.00); // Amount to be taken by the system

            $table->string('comission')->nullable(); // system percent commission take from the rider amount
            $table->decimal('rider_comission', 10, 2)->default(0.00); // Commission for the rider
            $table->decimal('reseller_comission', 10, 2)->default(0.00); // Commission for the reseller
            $table->decimal('vendor_comission', 10, 2)->default(0.00); // Commission for the vendor
            $table->decimal('system_comission', 10, 2)->default(0.00); // Commission for the system

            $table->text('weight')->nullable(); // weight
            $table->text('seller_note')->nullable();
            $table->text('rider_note')->nullable(); // Note from the rider
            $table->string('status')->default('Pending'); // e.g., pending, completed, failed
            $table->softDeletes(); // For soft deletion
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cod');
    }
};
