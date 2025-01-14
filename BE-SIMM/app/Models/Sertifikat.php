<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Sertifikat extends Model
{
    use HasFactory, HasUuids;

    protected $table = "sertifikat";

    protected $fillable = [
        "nomor_sertifikat",
        "id_peserta",
        "nama_penandatangan",
        "nip_penandatangan",
        "jabatan_penandatangan",
        "tanggal_penandatangan",
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }
}
