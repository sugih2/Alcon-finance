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
       
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('code')->unique();         
	        $table->unsignedBigInteger('fk_parposition')->nullable();
            $table->foreign('fk_parposition')
            ->references('id')
            ->on('param_positions')
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
        Schema::dropIfExists('positions');
    }
};
