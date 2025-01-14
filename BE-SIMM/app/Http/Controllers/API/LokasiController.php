<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lokasi;
use App\Models\User;
use App\Models\KepalaBagian;
use App\Http\Requests\LokasiRequest;
use App\Http\Controllers\API\KepalaBagianController;

class LokasiController extends Controller
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
        $lokasi = Lokasi::with(["list_peserta.lokasi", "list_peserta.institusi", "kepala_bagian.user",
        ])->get();

        return response()->json([
            "message" => "Lihat semua lokasi",
            "data" => $lokasi
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lokasi = Lokasi::with(["list_peserta.lokasi", "list_peserta.institusi", "list_peserta.status",
            "kepala_bagian.user", "kepala_bagian.lokasi",
        ])->find($id);

        if(!$lokasi){
            return response()->json([
                "message" => "ID lokasi tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data dengan ID: $id",
            "data" => $lokasi
        ]);
    }

    public function getLokasiOption()
    {
        $lokasi = Lokasi::whereColumn('kuota_terisi', '<', 'kuota_magang')->get();

        return response()->json([
            "message" => "Lihat semua lokasi",
            "data" => $lokasi
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LokasiRequest $request)
    {
        Lokasi::create($request->all());

        return response()->json([
            "message" => "Lokasi berhasil ditambahkan"
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(LokasiRequest $request, string $id)
    {
        $lokasi = Lokasi::find($id);

        if(!$lokasi){
            return response()->json([
                "message" => "ID lokasi tidak ditemukan"
            ], 404);
        }

        $data = $request->validated();
        $lokasi->update($data);
        return response()->json([
            "message" => "Lokasi berhasil diperbarui",
            "data" => $lokasi
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lokasi = Lokasi::find($id);

        if(!$lokasi){
            return response()->json([
                "message" => "ID lokasi tidak ditemukan"
            ], 404);
        }

        $kabag = KepalaBagian::where("id_lokasi", $lokasi->id)->first();
        if ($kabag) {
            // destroy user
            $user = User::where("id", $kabag->id_user)->first();
            $user->delete();

            // destroy kabag
            $kabagController = app()->make(KepalaBagianController::class);
            $kabagController->destroy($kabag->id);
        }
        
        $lokasi->delete();

        return response()->json([
            "message" => "Lokasi dengan ID $id berhasil dihapus"
        ]);
    }
}
