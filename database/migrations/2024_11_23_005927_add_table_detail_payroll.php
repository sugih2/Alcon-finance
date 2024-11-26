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
        Schema::create('detail_payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi');
            $table->foreign('id_transaksi')->references('id_transaksi')->on('master_payrolls')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('id_employee');
            $table->foreign('id_employee')->references('id')->on('employees')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('id_component');
            $table->foreign('id_component')->references('id')->on('param_componens')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_payrolls');
    }
};
