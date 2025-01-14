<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Models\Nilai;
use App\Models\JenisDokumen;
use App\Models\KeteranganPresensi;
use App\Models\TingkatPendidikan;

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

    public function getJenisDokumen()
    {
        $jenisDokumen = JenisDokumen::all();

        return response()->json([
            "message" => "Lihat semua jenis dokumen",
            "data" => $jenisDokumen
        ]);
    }

    public function getKeteranganPresensi()
    {
        $keteranganPresensi = KeteranganPresensi::all();

        return response()->json([
            "message" => "Lihat semua keterangan presensi",
            "data" => $keteranganPresensi
        ]);
    }

    public function getTingkatPendidikan()
    {
        $tingkatPendidikan = TingkatPendidikan::all();

        return response()->json([
            "message" => "Lihat semua tingkat pendidikan",
            "data" => $tingkatPendidikan
        ]);
    }
}
