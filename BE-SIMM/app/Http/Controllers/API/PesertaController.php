<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
use App\Models\Nilai;
use App\Models\Lokasi;
use App\Models\Presensi;
use App\Models\Logbook;
use App\Http\Requests\PesertaRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountInfo;

class PesertaController extends Controller
{
    public function __construct()
    {
        $this->middleware("isAdmin")->only("store", "destroy");
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $peserta = Peserta::all();

        return response()->json([
            "message" => "Lihat semua peserta",
            "data" => $peserta
        ]);
    }

    public function pesertaAktif()
    {
        $statusAktif = Status::where("nama_status", "Aktif")->first();
        $peserta = Peserta::with("lokasi", "institusi", "status")->where("id_status", $statusAktif->id)->get();
        return response()->json([
            "message" => "Lihat semua peserta aktif",
            "data" => $peserta
        ]);
    }

    public function arsipPeserta()
    {
        $statusAktif = Status::where("nama_status", "Aktif")->first();
        $peserta = Peserta::with(["lokasi", "institusi", "sertifikat", "nilai", "status", "dokumen"])
            ->where("id_status", "!=", $statusAktif->id)->get();
        return response()->json([
            "message" => "Lihat semua arsip peserta",
            "data" => $peserta
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PesertaRequest $request)
    {
        $data = $request->validated();

        // Jika file gambar diinput
        if ($request->hasFile("foto_profil")){
            $fotoName = time().".".$request->foto_profil->extension();
            $request->foto_profil->storeAs("public/foto_profil", $fotoName);
            $path = env("APP_URL")."/storage/foto_profil/";
            $data["foto_profil"] = $path.$fotoName;
        } 

        $rolePeserta = Role::where("nama_role", "Peserta")->first();
        $user = User::create([
            "email" => $request->email,
            "password" => Hash::make($request->nomor_induk), 
            "id_role" => $rolePeserta->id, 
        ]);

        $status = Status::where("nama_status", "Aktif")->first();
        $data["id_status"] = $status->id;
        $data["id_user"] = $user->id;

        $peserta = Peserta::create($data);

        Mail::to($user->email)->send(new AccountInfo($user, $peserta));

        if (isset($data["id_lokasi"])) {
            $lokasi = Lokasi::find($data["id_lokasi"]);
            if ($lokasi) {
                $lokasi->kuota_terisi += 1;
                $lokasi->save();
            }
        }

        return response()->json([
            "message" => "Peserta berhasil ditambahkan"
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $peserta = Peserta::with([
            "user", "lokasi", "status", "institusi", "nilai",
            "sertifikat", "logbook.lokasi", "presensi.lokasi", "dokumen" 
        ])->find($id);

        if(!$peserta){
            return response()->json([
                "message" => "ID peserta tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data dengan ID: $id",
            "data" => $peserta
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PesertaRequest $request, string $id)
    {
        $data = $request->validated();
        $peserta = Peserta::find($id);

        if(!$peserta){        
            return response()->json([
                "message" => "Data peserta dengan ID $id tidak ditemukan.",
            ], 404);
        }

        if ($request->hasFile("foto_profil")){
            if ($peserta->foto_profil) {
                $nameImage = basename($peserta->foto_profil);
                Storage::delete("public/foto_profil/" . $nameImage);
            }

            $fotoName = time() . "-foto_profil." . $request->foto_profil->extension();
            $request->foto_profil->storeAs("public/foto_profil/", $fotoName);

            $path = env("APP_URL") . "/storage/foto_profil/";
            $data["foto_profil"] = $path . $fotoName;
        }
        
        // Update kuota berdasarkan status
        $status = Status::where("nama_status", "Aktif")->first();
        if ($peserta->id_status === $status->id && $data["id_status"] !== $status->id) {
            // Dari status aktif ke selesai/batal
            $lokasi = Lokasi::find($data["id_lokasi"]);
            if ($lokasi) {
                $lokasi->kuota_terisi -= 1;
                $lokasi->save();
            }
        } else if ($peserta->id_status !== $status->id && $data["id_status"] === $status->id) {
            // Dari selesai/batal ke aktif
            $lokasi = Lokasi::find($data["id_lokasi"]);
            if ($lokasi) {
                $lokasi->kuota_terisi += 1;
                $lokasi->save();
            }
        }

        // Update kuota berdasarkan lokasi
        if ($data["id_lokasi"] !== $peserta->id_lokasi && $peserta->id_status === $status->id) {
            $oldLokasi = Lokasi::find($peserta->id_lokasi);
            if ($oldLokasi) {
                $oldLokasi->kuota_terisi -= 1;
                $oldLokasi->save();
            }

            $newLokasi = Lokasi::find($data["id_lokasi"]);
            if ($newLokasi) {
                $newLokasi->kuota_terisi += 1;
                $newLokasi->save();
            }
        }

        $peserta->update($data);

        return response()->json([
            "message" => "Peserta dengan ID $id berhasil diperbarui",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $peserta = Peserta::find($id);

        if(!$peserta){
            return response()->json([
                "message" => "ID Peserta tidak ditemukan"
            ], 404);
        }

        if($peserta->foto_profil){
            $fotoName = basename($peserta->foto_profil);
            Storage::delete("public/foto_profil/" . $fotoName);
        }

        $peserta->delete();

        return response()->json([
            "message" => "Peserta berhasil dihapus"
        ], 200);
    }
}
