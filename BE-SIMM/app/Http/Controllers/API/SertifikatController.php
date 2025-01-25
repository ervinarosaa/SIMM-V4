<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertifikat;
use App\Models\Peserta;
use App\Models\Penandatangan;
use App\Http\Requests\SertifikatRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class SertifikatController extends Controller
{
    public function __construct()
    {
        $this->middleware("isAdmin")->only("store", "generateCertificate");
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sertifikat = Sertifikat::all();

        return response()->json([
            "message" => "Lihat semua sertifikat",
            "data" => $sertifikat
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SertifikatRequest $request)
    {
        $data = $request->validated();

        // Find peserta
        $peserta = Peserta::find($data["id_peserta"]);

        if (!$peserta) {
            return response()->json([
                "message" => "Peserta tidak ditemukan!",
            ], 404);
        }

        // Find penandatangan
        $penandatangan = Penandatangan::find($data["id_penandatangan"]);

        if (!$penandatangan) {
            return response()->json([
                "message" => "Penandatangan tidak ditemukan!",
            ], 404);
        }

        // Create Sertifikat
        $sertifikat = Sertifikat::updateOrCreate(
            [
                "id_peserta" => $data["id_peserta"], 
            ],
            [
                "nomor_sertifikat" => $data["nomor_sertifikat"],
                "id_peserta" => $data["id_peserta"], 
                "id_penandatangan" => $data["id_penandatangan"], 
                "tanggal_sertifikat" => $data["tanggal_sertifikat"], 
            ]
        );

        // Update id_nilai pada peserta
        if ($peserta) {
            $peserta->update([
                "id_nilai" => $data["id_nilai"],
            ]);
        }

        return response()->json([
            "message" => "Sertifikat berhasil ditambahkan"
        ]);
    }

    public function generateCertificate($id)
    {
        $peserta = Peserta::with([
            "lokasi",
            "institusi",
            "nilai",
            "sertifikat.penandatangan",
        ])->find($id);
    
        // Ambil data sertifikat
        $sertifikat = $peserta->sertifikat;
    
        $fotoUrl = public_path("storage/foto_profil/" . basename($peserta->foto_profil));
    
        // Kirim $sertifikat ke view
        $pdf = Pdf::loadView("certificates.template", compact("peserta", "sertifikat", "fotoUrl"))
            ->setPaper("a4", "landscape");
    
        return $pdf->download("sertifikat-" . $peserta->nama_peserta . ".pdf");
    }
}
