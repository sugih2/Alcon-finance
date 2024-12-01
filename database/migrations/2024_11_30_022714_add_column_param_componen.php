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
        Schema::table('param_componens', function (Blueprint $table) {
            $table->unsignedBigInteger('id_regency')->nullable();
            $table->foreign('id_regency')
              ->references('id')
              ->on('regencies')
              ->onUpdate('cascade')
              ->onDelete('restrict');
            $table->unsignedBigInteger('id_position')->nullable();
            $table->foreign('id_position')
              ->references('id')
              ->on('positions')
              ->onUpdate('cascade')
              ->onDelete('restrict');
            $table->string('componen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('param_componens', function (Blueprint $table) {
            //
        });
    }
};
