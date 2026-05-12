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
        Schema::create('management_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applied_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('commission', 10, 2)->nullable();
            $table->text('message')->nullable();
            $table->boolean('status')->nullable();
            $table->foreignId('response_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('applied_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('management_teams');
    }
};
