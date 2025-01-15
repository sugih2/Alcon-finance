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
        Schema::create('deduction_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_payroll_history');
            $table->foreign('id_payroll_history')->references('id')->on('payroll_histories')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deduction_groups');
    }
};
