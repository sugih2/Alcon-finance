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
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')
              ->references('id')
              ->on('groups')
              ->onUpdate('cascade')
              ->onDelete('restrict');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')
              ->references('id')
              ->on('employees')
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
        Schema::table('group_members', function (Blueprint $table) {
            //
        });
    }
};
