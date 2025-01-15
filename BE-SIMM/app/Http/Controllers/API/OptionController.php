<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Models\Nilai;

class OptionController extends Controller
{
    public function getStatus()
    {
        $status = Status::all();

        return response()->json([
            "message" => "Lihat semua Status",
            "data" => $status
        ]);
    }

    public function getNilai()
    {
        $nilai = Nilai::all();

        return response()->json([
            "message" => "Lihat semua nilai",
            "data" => $nilai
        ]);
    }
}
