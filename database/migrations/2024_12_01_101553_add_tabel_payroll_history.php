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
        Schema::create('payroll_histories', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi_payment')->unique();
            $table->date('start_periode');
            $table->date('end_periode');
            $table->decimal('amount_transaksi', 10, 0);
            $table->integer('total_karyawan');
            $table->string('status_payroll');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_histories');
    }
};
