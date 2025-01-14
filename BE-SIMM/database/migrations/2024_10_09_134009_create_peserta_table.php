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
        Schema::create('peserta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_peserta');
            $table->string('nomor_induk');
            $table->text('alamat_peserta');
            $table->uuid('id_lokasi');
            $table->foreign('id_lokasi')->references('id')->on('lokasi')->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('id_status');
            $table->foreign('id_status')->references('id')->on('status')->onUpdate('cascade')->onDelete('cascade');
            $table->string('foto_profil')->nullable();
            $table->string('nama_pembimbing');
            $table->string('kontak_pembimbing');
            $table->string('fakultas');
            $table->string('jurusan');
            $table->uuid('id_institusi');
            $table->foreign('id_institusi')->references('id')->on('institusi')->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('id_nilai')->nullable();
            $table->foreign('id_nilai')->references('id')->on('nilai')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nomor_telepon');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->uuid('id_user');
            $table->foreign('id_user')->references('id')->on('users')->obUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
