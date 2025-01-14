<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Status extends Model
{
    use HasFactory, HasUuids;

    protected $table = "status";

    protected $fillable = [
        'nama_status'
    ];
}
