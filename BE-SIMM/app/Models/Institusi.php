<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Status;

class Institusi extends Model
{
    use HasFactory, HasUuids;

    protected $table = "institusi";

    protected $fillable = [
        "nama_institusi",
        "tingkat_pendidikan"
    ];

    public function list_peserta()
    {
        return $this->hasMany(Peserta::class, 'id_institusi');
    }

    public function list_peserta_aktif()
    {
        $statusAktif = Status::where("nama_status", "Aktif")->first();
        return $this->hasMany(Peserta::class, 'id_institusi')->where('id_status', $statusAktif->id);
    }

    public function tingkat_pendidikan()
    {
        return $this->belongsTo(TingkatPendidikan::class, 'id_tingkat');
    }
}
