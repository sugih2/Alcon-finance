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
        Schema::create('department_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->string('name');
            $table->unsignedBigInteger('id_department_team');
            $table->foreign('id_department_team')
              ->references('id')
              ->on('department_teams')
              ->onUpdate('cascade')
              ->onDelete('restrict');
            $table->unsignedBigInteger('id_manager');
            $table->foreign('id_manager')
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
        Schema::table('department_groups', function (Blueprint $table) {
            //
        });
    }
};
