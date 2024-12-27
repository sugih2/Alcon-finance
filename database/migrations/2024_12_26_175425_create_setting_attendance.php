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
        Schema::create('setting_attendances', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->integer('min_minutes'); // Batas bawah menit keterlambatan
            $table->integer('max_minutes')->nullable(); // Batas atas menit keterlambatan, NULL jika tidak terbatas
            $table->string('deduction_type'); // Tipe potongan (fixed atau percentage)
            $table->decimal('deduction_value', 8, 2); // Nilai potongan (jumlah tetap atau persentase dari nilaiPerHari)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_attendances');
    }
};
