<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->boolean('c')->default(false);
            $table->boolean('r')->default(false);
            $table->boolean('u')->default(false);
            $table->boolean('d')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_permissions');
    }
};
