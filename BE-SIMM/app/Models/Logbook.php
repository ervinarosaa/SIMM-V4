<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Logbook extends Model
{
    use HasFactory, HasUuids;

    protected $table = "logbook";

    protected $fillable = [
        "title",
        "deskripsi",
        "dokumentasi",
        "tanggal_logbook",
        "id_peserta",
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }
}
