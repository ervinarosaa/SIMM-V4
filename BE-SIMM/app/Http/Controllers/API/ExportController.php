<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Exports\PesertaAktifExport;
use App\Exports\ArsipPesertaExport;
use App\Exports\LogbookExport;
use App\Exports\PresensiExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportPesertaAktif(Request $request)
    {
        $startDate = $request->input("startDate");
        $endDate = $request->input("endDate");

        $fileName = "Laporan-Data-Magang-Aktif-(" . $startDate . "_" . $endDate . ").xlsx";

        // Membuat instance dari PesertaAktifExport
        $export = new PesertaAktifExport($startDate, $endDate);

        // Menggunakan Maatwebsite Excel untuk mendownload file
        return Excel::download($export, $fileName);
    }

    public function exportArsipPeserta(Request $request)
    {
        $startDate = $request->input("startDate");
        $endDate = $request->input("endDate");

        $fileName = "Laporan-Arsip-Data-Magang-(" . $startDate . "_" . $endDate . ").xlsx";

        // Membuat instance dari ArsipPesertaExport
        $export = new ArsipPesertaExport($startDate, $endDate);

        // Menggunakan Maatwebsite Excel untuk mendownload file
        return Excel::download($export, $fileName);
    }

    public function exportLogbook(Request $request)
    {
        $startDate = $request->input("startDate");
        $endDate = $request->input("endDate");
        $id_peserta = $request->input("id_peserta");

        if ($id_peserta) {
            $peserta = Peserta::find($id_peserta);

            if ($peserta) {
                $fileName = "Logbook-Magang-(" . $peserta->name . ").xlsx";
            } else {
                return response()->json(["error" => "Peserta tidak ditemukan"], 404);
            }
        } else {
            $fileName = "Logbook-Magang-(" . $startDate . "_" . $endDate . ").xlsx";
        }

        $export = new LogbookExport($startDate, $endDate, $id_peserta);
        return Excel::download($export, $fileName);
    }

    public function exportPresensi(Request $request)
    {
        $startDate = $request->input("startDate");
        $endDate = $request->input("endDate");
        $id_peserta = $request->input("id_peserta");

        if ($id_peserta) {
            $peserta = Peserta::find($id_peserta);

            if ($peserta) {
                $fileName = "Presensi-Magang-(" . $peserta->name . ").xlsx";
            } else {
                return response()->json(["error" => "Peserta tidak ditemukan"], 404);
            }
        } else {
            $fileName = "Presensi-Magang-(" . $startDate . "_" . $endDate . ").xlsx";
        }

        $export = new PresensiExport($startDate, $endDate, $id_peserta);
        return Excel::download($export, $fileName);
    }
}
