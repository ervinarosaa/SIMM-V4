<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Peserta extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = "string";
    protected $table = "peserta";

    protected $fillable = [
        "id", 
        'nama_peserta',
        'nomor_induk',
        'alamat_peserta',
        'id_lokasi',
        'id_status',
        'foto_profil',
        'nama_pembimbing',
        'kontak_pembimbing',
        'fakultas',
        'jurusan',
        'id_institusi',
        'id_nilai',
        'nomor_telepon',
        'tanggal_mulai',
        'tanggal_selesai',
        'id_user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'id_status');
    }

    public function institusi()
    {
        return $this->belongsTo(Institusi::class, 'id_institusi');
    }

    public function nilai()
    {
        return $this->belongsTo(Nilai::class, 'id_nilai');
    }
    
    public function logbook()
    {
        return $this->hasMany(Logbook::class, 'id_peserta');
    }
    
    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_peserta');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'id_peserta');
    }

    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class, 'id_peserta');
    }

    /**
     * Boot method to add custom logic.
     */
    protected static function booted()
    {
        static::creating(function ($peserta) {
            if (!$peserta->id) {
                $peserta->id = (string) \Str::uuid(); // Generate UUID
            }
        });
    }
}
