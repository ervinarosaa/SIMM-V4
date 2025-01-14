<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KepalaBagian extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'uuid';

    protected $table = "kepala_bagian";

    protected $fillable = [
        'nama_kabag',
        'nip_kabag',
        'id_user',
        'id_lokasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
}
