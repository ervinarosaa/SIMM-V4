<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KeteranganPresensi extends Model
{
    use HasFactory, HasUuids;

    protected $table = "keterangan_presensi";

    protected $fillable = [
        'nama_keterangan'
    ];
}
