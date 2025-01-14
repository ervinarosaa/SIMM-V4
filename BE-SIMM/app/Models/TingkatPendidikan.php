<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TingkatPendidikan extends Model
{
    use HasFactory, HasUuids;

    protected $table = "tingkat_pendidikan";

    protected $fillable = [
        'nama_tingkat'
    ];
}
