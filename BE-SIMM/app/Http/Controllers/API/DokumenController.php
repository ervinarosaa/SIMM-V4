<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dokumen;
use App\Models\Peserta;
use App\Models\User;
use App\Models\JenisDokumen;
use App\Http\Requests\DokumenRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\DokumenMail;

class DokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dokumen = Dokumen::with("peserta", "jenis")->get();

        return response()->json([
            "message" => "Lihat semua Dokumen",
            "data" => $dokumen
        ]);
    }

    private function handleRequest(DokumenRequest $request)
    {
        $data = $request->validated();

        $peserta = Peserta::find($data["id_peserta"]);

        if(!$peserta){
            return response()->json([
                "message" => "Peserta tidak ditemukan!"
            ], 404);
        }

        $existingDokumen = Dokumen::where([
            "id_peserta" => $data["id_peserta"],
            "id_jenis" => $data["id_jenis"],
        ])->first();

        if ($request->hasFile("file")){
            // Hapus file lama jika dokumen ada
            if ($existingDokumen && $existingDokumen->file) {
                $fileName = basename($existingDokumen->file);
                Storage::delete("public/dokumen/" . $fileName);
            }

            // Membuat unique name
            $fileName = time().".".$request->file->extension();

            // Simpan pada file storage
            $request->file->storeAs("public/dokumen", $fileName);

            // Mengganti nilai request pada file menjadi $fileName yang baru
            $path = env("APP_URL")."/storage/dokumen/";
            $data["file"] = $path.$fileName;
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DokumenRequest $request)
    {
        $data = $this->handleRequest($request);

        Dokumen::updateOrCreate(
            [
                "id_peserta" => $data["id_peserta"],
                "id_jenis" => $data["id_jenis"],
            ],
            $data
        );

        return response()->json([
            "message" => "Dokumen berhasil ditambahkan"
        ]);
    }

    public function sendMail(DokumenRequest $request)
    {
        $data = $this->handleRequest($request);

        $dokumen = Dokumen::updateOrCreate(
            [
                "id_peserta" => $data["id_peserta"],
                "id_jenis" => $data["id_jenis"],
            ],
            $data
        );

        $jenisDokumen = JenisDokumen::findOrFail($request->id_jenis);
        $peserta = Peserta::find($dokumen->id_peserta);
        $user = User::where("id", $peserta->id_user)->first();

        Mail::to($user->email)->send(new DokumenMail($peserta, $dokumen, $jenisDokumen));

        return response()->json([
            "message" => "Dokumen berhasil ditambahkan dan dikirim ke email peserta"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dokumen = Dokumen::with(["peserta", "jenis"])->find($id);

        if(!$dokumen){
            return response()->json([
                "message" => "ID Dokumen tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data dengan ID: $id",
            "data" => $dokumen
        ]);
    }

    public function DokumenByIdPeserta($id_peserta)
    {
        $dokumen = Dokumen::with(["peserta", "jenis"])->where("id_peserta", $id_peserta)->get();

        if ($dokumen->isEmpty()) {
            return response()->json([
                "message" => "Dokumen untuk peserta dengan ID: $id_peserta tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data dokumen untuk peserta dengan ID: $id_peserta",
            "data" => $dokumen
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dokumen = Dokumen::find($id);

        if(!$dokumen){
            return response()->json([
                "message" => "Dokumen tidak ditemukan!"
            ], 404);
        }

        if($dokumen->file){
            $fileName = basename($dokumen->file);
            Storage::delete("public/dokumen/" . $fileName);
        }

        $dokumen->delete();

        return response()->json([
            "message" => "Dokumen berhasil dihapus"
        ], 200);
    }
}
