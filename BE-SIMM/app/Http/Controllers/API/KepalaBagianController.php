<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\KepalaBagian;
use App\Models\Lokasi;
use App\Http\Requests\KepalaBagianRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\KabagInfo;

class KepalaBagianController extends Controller
{
    public function __construct()
    {
        $this->middleware("isAdmin")->only("store", "destroy");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KepalaBagianRequest $request)
    {
        // Memeriksa user
        $kabag = KepalaBagian::where("id_lokasi", $request->id_lokasi)->first();
        $user = null;
        if ($kabag) {
            $user = User::where("id", $kabag->id_user)->first();
        }

        $request->validate([// Validasi input form
            "nama_kabag" => "required|max:255",
            "nip_kabag" => "required|max:255",
            "email" => $user ? "required|email" : "required|email|unique:users,email",
            "id_lokasi" => "required|exists:lokasi,id",
        ]);

        $roleKabag = Role::where("nama_role", "Kepala Bagian")->first();
        $password = substr($request->nip_kabag, 0, 10);
        if ($user) {
            // Update user jika ditemukan
            $user->update([
                "email" => $request->email,
                "password" => Hash::make($password),
                "id_role" => $roleKabag->id,
            ]);
        } else {
            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($password), 
                "id_role" => $roleKabag->id, 
            ]);
        }
        
        $kabag = KepalaBagian::updateOrCreate(
            [
                "id_user" => $user->id, 
                "id_lokasi" => $request->id_lokasi,
            ],
            [
                "nip_kabag" => $request->nip_kabag,
                "nama_kabag" => $request->nama_kabag,
                "id_user" => $user->id, 
                "id_lokasi" => $request->id_lokasi,
            ]
        );

        $lokasi = Lokasi::where("id", $request->id_lokasi)->first();
        Mail::to($user->email)->send(new KabagInfo($user, $kabag, $lokasi));

        return response()->json([
            "message" => "Kepala bagian berhasil disimpan"
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kabag = KepalaBagian::find($id);

        if(!$kabag){
            return response()->json([
                "message" => "ID Kepala Bagian tidak ditemukan"
            ], 404);
        }

        $kabag->delete();
        return response()->json([
            "message" => "Kepala Bagian dengan ID $id berhasil dihapus"
        ]);
    }
}
