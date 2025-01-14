<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lokasi extends Model
{
    use HasFactory, HasUuids;

    protected $table = "lokasi";

    protected $fillable = [
        "nama_lokasi",
        "alamat_lokasi",
        "kuota_magang",
        "kuota_terisi",
    ];

    public function list_peserta()
    {
        return $this->hasMany(Peserta::class, "id_lokasi");
    }

    public function kepala_bagian()
    {
        return $this->hasOne(KepalaBagian::class, "id_lokasi", "id");
    }
}
