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
        Schema::create('kepala_bagian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kabag');
            $table->string('nip_kabag');
            $table->uuid('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('kepala_bagian');
    }
};
