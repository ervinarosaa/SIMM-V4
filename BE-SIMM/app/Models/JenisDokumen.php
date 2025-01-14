<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JenisDokumen extends Model
{
    use HasFactory, HasUuids;

    protected $table = "jenis_dokumen";

    protected $fillable = [
        'nama_jenis'
    ];
}
