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
        Schema::create('logbook', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('deskripsi');
            $table->string('dokumentasi')->nullable();
            $table->date('tanggal_logbook');
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
        Schema::dropIfExists('logbook');
    }
};
