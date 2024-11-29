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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nik', 16)->unique();
            $table->string('name');
            $table->string('phone', 15);
            $table->string('address', 255);
            $table->string('email', 50);
            $table->date('birth_date');
            $table->unsignedBigInteger('position_id');
            $table->foreign('position_id')
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
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
