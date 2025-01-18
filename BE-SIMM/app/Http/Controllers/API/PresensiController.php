<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Presensi;
use App\Models\Peserta;
use App\Models\Lokasi;
use App\Http\Requests\PresensiRequest;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presensi = Presensi::with(["peserta.institusi","peserta.lokasi"])->get();

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

        $lokasi = Lokasi::find($peserta->id_lokasi);
        if (!$lokasi) {
            return response()->json([
                "message" => "Lokasi magang peserta tidak ditemukan!"
            ], 404);
        }

        $distance = $this->calculateDistance(
            $data["latitude_presensi"],
            $data["longitude_presensi"],
            $lokasi->latitude_lokasi,
            $lokasi->longitude_lokasi
        );

        if ($distance > 50) {
            return response()->json([
                "message" => "Anda berada di luar radius 50 meter dari lokasi magang!",
                "distance" => $distance,
                "debug" => [
                            "latitude_presensi" => $data["latitude_presensi"],
                            "longitude_presensi" => $data["longitude_presensi"],
                            "latitude_lokasi" => $lokasi->latitude_lokasi,
                            "longitude_lokasi" => $lokasi->longitude_lokasi,
                ],
            ], 422);
        }

        try {
            Presensi::create([
                "tanggal_presensi" => $data["tanggal_presensi"],
                "longitude_presensi" => $data["longitude_presensi"],
                "latitude_presensi" => $data["latitude_presensi"],
                "keterangan_presensi" => $data["keterangan_presensi"],
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

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        // Konversi derajat ke radian
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Haversine formula
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Jarak dalam meter
        return $earthRadius * $c;
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $presensi = Presensi::with(["peserta.institusi", "peserta.lokasi"])->find($id);

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

    public function PresensiByIdPeserta($id_peserta)
    {
        $presensi = Presensi::with(["peserta.institusi", "peserta.lokasi"])
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
