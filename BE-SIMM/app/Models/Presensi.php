<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Presensi extends Model
{
    use HasFactory, HasUuids;

    protected $table = "presensi";

    protected $fillable = [
        "tanggal_presensi",
        "keterangan_presensi",
        "latitude_presensi",
        "longitude_presensi",
        "id_peserta",
        "id_lokasi",
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
}
