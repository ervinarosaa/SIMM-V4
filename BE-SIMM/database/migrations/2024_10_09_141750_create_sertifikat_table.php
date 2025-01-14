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
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_sertifikat');
            $table->uuid('id_peserta');
            $table->foreign('id_peserta')->references('id')->on('peserta')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nama_penandatangan');
            $table->string('nip_penandatangan');
            $table->string('jabatan_penandatangan');
            $table->string('tanggal_penandatangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
