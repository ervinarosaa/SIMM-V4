<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Penandatangan extends Model
{
    use HasFactory, HasUuids;

    protected $table = "penandatangan";

    protected $fillable = [
        "nama_penandatangan",
        "nip_penandatangan",
        "jabatan_penandatangan",
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }
}
