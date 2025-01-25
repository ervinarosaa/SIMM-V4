<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penandatangan;
use App\Http\Requests\PenandatanganRequest;

class PenandatanganController extends Controller
{
    public function index()
    {
        $penandatangan = Penandatangan::all();

        return response()->json([
            "message" => "Lihat semua penandatangan",
            "data" => $penandatangan
        ]);
    }

    public function store(PenandatanganRequest $request)
    {
        Penandatangan::create($request->all());

        return response()->json([
            "message" => "Penandatangan berhasil ditambahkan"
        ]);
    }
}
