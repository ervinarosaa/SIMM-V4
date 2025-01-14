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
        "latitude",
        "longitude",
        "id_keterangan",
        "id_peserta",
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }

    public function keterangan()
    {
        return $this->belongsTo(KeteranganPresensi::class, "id_keterangan");
    }
}
