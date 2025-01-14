<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Peserta;
use App\Models\KepalaBagian;
use App\Models\Admin;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function getUser() {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return response()->json([
                "message" => "User not authenticated",
            ], 401);
        }
    
        $role = Role::find($currentUser->id_role);
        if (!$role) {
            return response()->json([
                "message" => "Role not found",
            ], 404);
        }
    
        $user = User::with("role")
            ->when($role->nama_role === "Admin", function($query) {
                $query->with("admin");
            })
            ->when($role->nama_role === "Kepala Bagian", function($query) {
                $query->with("kepala_bagian");
            })
            ->where("id", $currentUser->id)
            ->first();
    
        if (!$user) {
            return response()->json([
                "message" => "Pengguna tidak ditemukan",
            ], 404);
        }

        if ($role->nama_role === "Peserta") {
            $peserta = $user->peserta; 
        }
    
        return response()->json([
            "message" => "Get user was successful",
            "user" => $user,
        ]);
    }

    public function login(Request $request) {
        $credentials = request(["email", "password"]);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                "message" => "User Invalid"
            ], 401);
        }

        $user = User::with("Role")->where("email", $request["email"])->first();
        $token = JWTAuth::fromUser($user);
        return response()->json([
            "user" => $user,
            "token" => $token,
        ]);
    }

    public function generateOtpCode(Request $request){
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                "message" => "Email tidak terdaftar. Silahkan masukkan alamat email Anda yang telah terdaftar pada sistem!"
            ], 404);
        }

        $role = Role::where("id", $user->id_role)->first();
        $user->generateOtpCode();
        
        if ($role->nama_role == "Kepala Bagian") {
            $kabag = KepalaBagian::where("id_user", $user->id)->first();
            Mail::to($request->email)->send(new OtpMail($user, $kabag, $role->nama_role));
        } elseif ($role->nama_role == "Peserta") {
            $peserta = Peserta::where("id_user", $user->id)->first();
            Mail::to($request->email)->send(new OtpMail($user, $peserta, $role->nama_role));
        } elseif ($role->nama_role == "Admin") {
            $admin = Admin::where("id_user", $user->id)->first();
            Mail::to($request->email)->send(new OtpMail($user, $admin, $role->nama_role));
        }
    
        return response()->json([
            "message" => "Berhasil generate OTP Code",
            "data" => $user
        ]);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required',
            'otp' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        // check otp code in DB
        $otp_codes = OtpCode::where('otp', $request->otp)->first();

        if(!$otp_codes){
            return response()->json([
                "message" => "Kode OTP salah. Masukan kode yang telah dikirimkan ke email Anda!"
            ], 404);
        }

        // Check OTP Code expired
        $now = Carbon::now();
        if($now > $otp_codes->valid_until){
            return response()->json([
                "message" => "Kode OTP telah kadaluwarsa. Silahkan request kode OTP kembali!"
            ], 400);
        }

        // Find user associated with the OTP
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                "message" => "Pengguna tidak ditemukan"
            ], 404);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Delete OTP code after successful password reset
        $otp_codes->delete();

        $role = Role::where("id", $user->id_role)->first();
        
        if ($role->nama_role == "Kepala Bagian") {
            $kabag = KepalaBagian::where("id_user", $user->id)->first();
            Mail::to($request->email)->send(new ResetPassword($user, $kabag, $role->nama_role));
        } elseif ($role->nama_role == "Peserta") {
            $peserta = Peserta::where("id_user", $user->id)->first();
            Mail::to($request->email)->send(new ResetPassword($user, $peserta, $role->nama_role));
        } elseif ($role->nama_role == "Admin") {
            $admin = Admin::where("id_user", $user->id)->first();
            Mail::to($request->email)->send(new ResetPassword($user, $admin, $role->nama_role));
        }

        return response()->json([
            "message" => "Password berhasil diubah!"
        ], 200);
    }

    public function logout() {
        auth()->logout();

        return response()->json([
            "message" => "Logout Success"
        ]);
    }
}
