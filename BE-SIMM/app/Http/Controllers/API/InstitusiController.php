<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institusi;
use App\Http\Requests\InstitusiRequest;

class InstitusiController extends Controller
{
    public function __construct()
    {
        $this->middleware("isAdmin")->only("store", "update", "destroy");
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $institusi = Institusi::with("list_peserta")->get();

        return response()->json([
            "message" => "Lihat semua institusi",
            "data" => $institusi
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InstitusiRequest $request)
    {
        Institusi::create($request->all());

        return response()->json([
            "message" => "Institusi berhasil ditambahkan"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $institusi = Institusi::with(["list_peserta.lokasi", "list_peserta.institusi", "list_peserta.status"])->find($id);

        if(!$institusi){
            return response()->json([
                "message" => "ID Institusi tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data dengan ID: $id",
            "data" => $institusi
        ]);
    }
    
    public function institusiPesertaAktif($id)
    {
        $Institusi = Institusi::with("list_peserta_aktif")->find($id);

        return response()->json([
            "message" => "Lihat Institusi dengan peserta aktif",
            "data" => $Institusi
        ]);
    }

    public function universitas()
    {
        $universitas = Institusi::with(["list_peserta_aktif"])->where("tingkat_pendidikan", "Perguruan Tinggi")->get();
        return response()->json([
            "message" => "Lihat semua universitas",
            "data" => $universitas
        ]);
    }

    public function sekolah()
    {
        $sekolah = Institusi::with(["list_peserta_aktif"])->where("tingkat_pendidikan", "Sekolah Kejuruan")->get();
        return response()->json([
            "message" => "Lihat semua sekolah",
            "data" => $sekolah
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InstitusiRequest $request, string $id)
    {
        $institusi = Institusi::find($id);

        $data = $request->validated();
        $institusi->update($data);

        return response()->json([
            "message" => "Institusi berhasil diperbarui",
            "data" => $institusi
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $institusi = Institusi::find($id);
        if(!$institusi){
            return response()->json([
                "message" => "ID Institusi tidak ditemukan"
            ], 404);
        }

        $institusi->delete();
        return response()->json([
            "message" => "Institusi dengan ID $id berhasil dihapus"
        ]);
    }
}
