<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Status;
use App\Models\Institusi;
use App\Models\Lokasi;
use App\Models\Presensi;
use App\Models\Logbook;
use App\Models\Dokumen;

class DashboardController extends Controller
{
    public function dahboardAdmin()
    {
        try {
            $statusAktif = Status::where("nama_status", "Aktif")->first();
            $totalPesertaAktif = Peserta::where("id_status", $statusAktif->id)->count();

            $totalInstitusi = Institusi::count();

            $totalLokasi = Lokasi::count();

            $totalKuotaMagang = Lokasi::sum("kuota_magang");

            return response()->json([
                "message" => "Berhasil menampilkan dashboard!",
                "data" => [
                    "total_peserta_aktif" => $totalPesertaAktif,
                    "total_institusi" => $totalInstitusi,
                    "total_lokasi" => $totalLokasi,
                    "total_kuota_magang" => $totalKuotaMagang,
                ],
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                "status" => "error",
                "message" => "Failed to load dashboard data",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function distribusiPesertaByInstitusi()
    {
        try {
            $statusAktif = Status::where("nama_status", "Aktif")->first();

            $distribusiPeserta = Institusi::selectRaw("institusi.id, institusi.nama_institusi, COUNT(peserta.id) as total_peserta_aktif")
                ->join("peserta", "peserta.id_institusi", "=", "institusi.id")
                ->where("peserta.id_status", $statusAktif->id)
                ->groupBy("institusi.id", "institusi.nama_institusi") 
                ->orderBy("total_peserta_aktif", "desc")
                ->take(4)
                ->get()
                ->map(function ($item) {
                    return [
                        "id" => $item->id,
                        "name" => $item->nama_institusi,
                        "total_peserta" => $item->total_peserta_aktif,
                    ];
                });
            
            $distribusiPeserta = collect($distribusiPeserta);

            return response()->json([
                "status" => "Berhasil menampilkan distribusi peserta berdasarkan institusi!",
                "data" => $distribusiPeserta,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to load distribution data",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function distribusiTingkatPendidikan() 
    {
        try {
            $totalPerguruanTinggi = Institusi::where("tingkat_pendidikan", "Perguruan Tinggi")->count();
            $totalSekolahKejuruan= Institusi::where("tingkat_pendidikan", "Sekolah Kejuruan")->count();
            
            return response()->json([
                "message" => "Berhasil menampilkan distribusi tingkat pendidikan!",
                "data" => [
                    [
                        "name" => "Perguruan Tinggi",
                        "total_peserta" => $totalPerguruanTinggi,
                    ],
                    [
                        "name" => "Sekolah Kejuruan",
                        "total_peserta" => $totalSekolahKejuruan,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to load distribution data",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function distribusiPesertaByLokasi()
    {
        try {
            $statusAktif = Status::where("nama_status", "Aktif")->first();

            // Distribusi Peserta Berdasarkan Lokasi
            $distribusiPeserta = Peserta::selectRaw("id_lokasi, COUNT(*) as total_peserta")
                ->where("peserta.id_status", $statusAktif->id)
                ->groupBy("id_lokasi")
                ->with("lokasi:id,nama_lokasi") // Eager load data lokasi
                ->get()
                ->map(function ($item) {
                    return [
                        "id_lokasi" => $item->id_lokasi,
                        "nama_lokasi" => $item->lokasi->nama_lokasi ?? "Unknown", 
                        "total_peserta" => $item->total_peserta, 
                    ];
                });

            $distribusiPeserta = collect($distribusiPeserta);

            // Urutkan Berdasarkan Total Peserta (Desc)
            $distribusiPeserta = $distribusiPeserta->sortByDesc("total_peserta")->values();

            return response()->json([
                "status" => "success",
                "data" => $distribusiPeserta,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to load distribution data by location",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function pesertaByStatus()
    {
        try {
            $distribusiPeserta = Status::selectRaw("
                status.id, 
                status.nama_status, 
                COUNT(DISTINCT peserta.id) as total_peserta
            ")
                ->leftJoin("peserta", "status.id", "=", "peserta.id_status")
                ->groupBy("status.id", "status.nama_status") 
                ->get()
                ->map(function ($item) {
                    return [
                        "id_status" => $item->id,
                        "nama_status" => $item->nama_status,
                        "total_peserta" => $item->total_peserta
                    ];
                });

            return response()->json([
                "status" => "Berhasil menampilkan distribusi peserta berdasarkan status!",
                "data" => $distribusiPeserta,
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                "status" => "error",
                "message" => "Failed to load dashboard data",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function presensiPeserta($id)
    {
        try {
            $total_hadir = Presensi::where("id_peserta", $id)
                ->where("keterangan_presensi", "Hadir")
                ->count();

            $total_izin = Presensi::where("id_peserta", $id)
                ->where("keterangan_presensi", "Izin")
                ->count();

            $total_sakit = Presensi::where("id_peserta", $id)
                ->where("keterangan_presensi", "Sakit")
                ->count();

            return response()->json([
                "status" => "Berhasil menampilkan distribusi presensi!",
                "data" => [
                    [
                        "keterangan_name" => "Hadir",
                        "total_presensi" => $total_hadir,
                    ],
                    [
                        "keterangan_name" => "Izin",
                        "total_presensi" => $total_izin,
                    ],
                    [
                        "keterangan_name" => "Sakit",
                        "total_presensi" => $total_sakit,
                    ],
                ],
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                "status" => "error",
                "message" => "Failed to load dashboard data",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function logbookPeserta($id)
    {
        try {
            $total_presensi = Presensi::where("id_peserta", $id)->count();

            $total_logbook = Logbook::where("id_peserta", $id)->count();

            $belum_dibuat = max(0, $total_presensi - $total_logbook);

            return response()->json([
                "status" => "Berhasil menampilkan distribusi logbook!",
                "data" => [
                    [
                        "name" => "Belum Dibuat",
                        "total_logbook" => $belum_dibuat,
                    ],
                    [
                        "name" => "Sudah Dibuat",
                        "total_logbook" => $total_logbook,
                    ],
                ],
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                "status" => "error",
                "message" => "Failed to load dashboard data",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function administrasiPeserta($id)
    {
        try {
            $total_administrasi = 4;

            $total_tersedia = Dokumen::where("id_peserta", $id)->count();

            $belum_tersedia = max(0, $total_administrasi - $total_tersedia);

            return response()->json([
                "status" => "Berhasil menampilkan distribusi administrasi!",
                "data" => [
                    [
                        "name" => "Belum Tersedia",
                        "total_dokumen" => $belum_tersedia,
                    ],
                    [
                        "name" => "Tersedia",
                        "total_dokumen" => $total_tersedia,
                    ],
                ],
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                "status" => "error",
                "message" => "Failed to load dashboard data",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
