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
        Schema::create('presensi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('tanggal_presensi');
            $table->string('keterangan_presensi');
            $table->string('latitude_presensi');
            $table->string('longitude_presensi');
            $table->uuid('id_peserta');
            $table->foreign('id_peserta')->references('id')->on('peserta')->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('id_lokasi');
            $table->foreign('id_lokasi')->references('id')->on('lokasi')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
