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
        Schema::create('payroll_process_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_payroll_history')->constrained('payroll_histories')->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('payment_amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['success', 'failed'])->default('success');
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_process_details');
    }
};
