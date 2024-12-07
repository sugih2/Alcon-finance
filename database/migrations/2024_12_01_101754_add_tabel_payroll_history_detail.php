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
        Schema::create('payroll_history_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_payroll_history');
            $table->foreign('id_payroll_history')->references('id')->on('payroll_histories')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->string('id_transaksi_payment');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')
                ->onDelete('restrict');
            $table->decimal('salary', 10, 2);
            $table->json('allowance');
            $table->json('deduction');
            $table->decimal('total_pendapatan', 10, 2);
            $table->decimal('total_potongan', 10, 2);
            $table->decimal('gaji_bruto', 10, 2);
            $table->decimal('gaji_bersih', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_history_details');
    }
};
