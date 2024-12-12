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
        Schema::create('attendance_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_payroll_history_detail');
            $table->date('tanggal');
            $table->decimal('earnings', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->string('deduction_reason')->nullable();
            $table->timestamps();

            $table->foreign('id_payroll_history_detail')->references('id')->on('payroll_history_details')->onDelete('cascade');
            
            $table->index(['id_payroll_history_detail', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_details');
    }
};
