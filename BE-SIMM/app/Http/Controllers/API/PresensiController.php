<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Presensi;
use App\Models\Peserta;
use App\Http\Requests\PresensiRequest;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presensi = Presensi::with(["peserta.institusi","peserta.lokasi","keterangan",])->get();

        return response()->json([
            "message" => "Lihat semua presensi",
            "data" => $presensi
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PresensiRequest $request)
    {
        $data = $request->validated();
        $peserta = Peserta::find($data["id_peserta"]);
        if(!$peserta){
            return response()->json([
                "message" => "Peserta tidak ditemukan!"
            ], 404);
        }

        $existingPresensi = Presensi::where('id_peserta', $data["id_peserta"])
                                ->where('tanggal_presensi', $data["tanggal_presensi"])
                                ->first();
        if ($existingPresensi) {
            return response()->json([
                "message" => "Anda sudah melakukan presensi hari ini!"
            ], 422);
        }

        try {
            Presensi::create([
                "tanggal_presensi" => $data["tanggal_presensi"],
                "longitude" => $data["longitude"],
                "latitude" => $data["latitude"],
                "id_keterangan" => $data["id_keterangan"],
                "id_peserta" => $data["id_peserta"],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Gagal menyimpan presensi!",
                "error" => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            "message" => "Presensi berhasil ditambahkan!"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $presensi = Presensi::with([
            "peserta.institusi",
            "peserta.lokasi",
            "keterangan",
        ])->find($id);

        if(!$presensi){
            return response()->json([
                "message" => "ID Presensi tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data dengan ID: $id",
            "data" => $presensi
        ]);
    }

    public function checkDuplicate(Request $request)
    {
        $idPeserta = $request->query('id_peserta');
        $tanggalPresensi = $request->query('tanggal_presensi');
        $tanggalOnly = Carbon::parse($tanggalPresensi)->toDateString();

        $exists = Presensi::where('id_peserta', $idPeserta)
                    ->whereDate('tanggal_presensi', $tanggalOnly)
                    ->exists();

        return response()->json([
            "message" => "Data presensi sudah ada!",
            "data" => $exists
        ]);
    }

    public function PresensiByIdPeserta($id_peserta)
    {
        $presensi = Presensi::with(["peserta.institusi", "peserta.lokasi", "keterangan"])
            ->where("id_peserta", $id_peserta)->get();
        
        if ($presensi->isEmpty()) {
            return response()->json([
                "message" => "Presensi untuk peserta dengan ID: $id_peserta tidak ditemuka"
            ], 404);
        }

        return response()->json([
            "message" => "Data Presensi untuk peserta dengan ID: $id_peserta",
            "data" => $presensi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $presensi = Presensi::find($id);

        if(!$presensi){
            return response()->json([
                "message" => "ID Presensi tidak ditemukan"
            ], 404);
        }

        $presensi->delete();
        return response()->json([
            "message" => "Presensi dengan ID $id berhasil dihapus"
        ]);
    }
}
