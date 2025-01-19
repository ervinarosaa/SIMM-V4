<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Peserta;
use App\Http\Requests\LogbookRequest;
use Illuminate\Support\Facades\Storage;

class LogbookController extends Controller
{
    public function __construct()
    {
        $this->middleware("isPeserta")->only("store", "update", "destroy");
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logbook = Logbook::with([
            "peserta.institusi",
            "lokasi",
        ])->get();

        return response()->json([
            "message" => "Lihat semua Logbook",
            "data" => $logbook
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LogbookRequest $request)
    {
        $data = $request->validated();

        // Find peserta
        $peserta = Peserta::find($data["id_peserta"]);

        if (!$peserta) {
            return response()->json([
                "message" => "Peserta tidak ditemukan!"
            ], 404);
        }

        //File Image Dokumentasi Logbook
        if ($request->hasFile("dokumentasi")){
            $imageName = time().".".$request->dokumentasi->extension();
            $request->dokumentasi->storeAs("public/logbook", $imageName);
            $path = env("APP_URL")."/storage/logbook/";
            $data["dokumentasi"] = $path.$imageName;
        }

        // Store Logbook
        Logbook::create([
            "title" => $data["title"],
            "deskripsi" => $data["deskripsi"],
            "dokumentasi" => $data["dokumentasi"],
            "tanggal_logbook" => $data["tanggal_logbook"],
            "id_peserta" => $data["id_peserta"],
            "id_lokasi" => $data["id_lokasi"],
        ]);

        return response()->json([
            "message" => "Logbook berhasil ditambahkan"
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $logbook = Logbook::with([
            "peserta.institusi",
            "lokasi",
        ])->find($id);

        if(!$logbook){
            return response()->json([
                "message" => "ID Logbook tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data dengan ID: $id",
            "data" => $logbook
        ]);
    }

    public function LogbookByIdPeserta($id_peserta)
    {
        $logbook = Logbook::with(["peserta.institusi","lokasi",])
            ->where("id_peserta", $id_peserta)->get();

        if ($logbook->isEmpty()) {
            return response()->json([
                "message" => "Logbook untuk peserta dengan ID: $id_peserta tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data logbook untuk peserta dengan ID: $id_peserta",
            "data" => $logbook
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LogbookRequest $request, string $id)
    {
        $logbook = Logbook::find($id);

        if(!$logbook){
            return response()->json([
                "message" => "ID Logbook tidak ditemukan"
            ], 404);
        }

        $data = $request->validated();
        $peserta = Peserta::find($data["id_peserta"]);

        if(!$peserta){
            return response()->json([
                "message" => "Peserta tidak ditemukan!"
            ], 404);
        }

        if ($request->hasFile("dokumentasi")){
            if ($logbook->dokumentasi) {
                $nameImage = basename($logbook->dokumentasi);
                Storage::delete("public/logbook/" . $nameImage);
            }

            $fotoName = time() . "-dokumentasi." . $request->dokumentasi->extension();
            $request->dokumentasi->storeAs("public/logbook/", $fotoName);

            $path = env("APP_URL") . "/storage/logbook/";
            $data["dokumentasi"] = $path . $fotoName;
        }

        $logbook->update($data);

        return response()->json([
            "message" => "Logbook dengan ID $id berhasil diperbarui"
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $logbook = Logbook::find($id);

        if(!$logbook){
            return response()->json([
                "message" => "ID Logbook tidak ditemukan!"
            ], 404);
        }

        if($logbook->dokumentasi){
            $fotoName = basename($logbook->dokumentasi);
            Storage::delete("public/logbook/" . $fotoName);
        }

        $logbook->delete();

        return response()->json([
            "message" => "Logbook berhasil dihapus"
        ], 200);
    }
}
