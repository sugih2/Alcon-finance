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
        Schema::create('department_teams', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->string('name');
            $table->unsignedBigInteger('id_department');
            $table->foreign('id_department')
              ->references('id')
              ->on('departments')
              ->onUpdate('cascade')
              ->onDelete('restrict');
            $table->unsignedBigInteger('id_position');
            $table->foreign('id_position')
              ->references('id')
              ->on('positions')
              ->onUpdate('cascade')
              ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_teams', function (Blueprint $table) {
            //
        });
    }
};
