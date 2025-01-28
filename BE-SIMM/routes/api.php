<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    AuthController,
    LokasiController,
    KepalaBagianController,
    InstitusiController,
    PesertaController,
    SertifikatController,
    LogbookController,
    PresensiController,
    DokumenController,
    OptionController,
    ExportController,
    DashboardController,
    PenandatanganController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::apiResource("lokasi", LokasiController::class)->middleware('auth:api');
    Route::apiResource("institusi", InstitusiController::class)->middleware('auth:api');
    Route::apiResource("peserta", PesertaController::class)->middleware('auth:api');
    Route::apiResource("sertifikat", SertifikatController::class)->middleware('auth:api');
    Route::apiResource("logbook", LogbookController::class)->middleware('auth:api');
    Route::apiResource("presensi", PresensiController::class)->middleware('auth:api');
    Route::apiResource("dokumen", DokumenController::class)->middleware('auth:api');
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('api');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
        Route::post('generate-otp-code', [AuthController::class, 'generateOtpCode']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });
    Route::get('me', [AuthController::class, 'getUser'])->middleware('auth:api');
    Route::get("peserta-aktif", [PesertaController::class, "pesertaAktif"])->middleware('auth:api');
    Route::get("peserta-arsip", [PesertaController::class, "arsipPeserta"])->middleware('auth:api');
    Route::patch('/peserta/{id}/selesai', [PesertaController::class, 'updateStatusSelesai'])->middleware('auth:api');
    Route::get("lokasi-option", [LokasiController::class, "getLokasiOption"])->middleware('auth:api');
    Route::get("institusi-aktif/{id}", [InstitusiController::class, "institusiPesertaAktif"])->middleware('auth:api');
    Route::get("universitas", [InstitusiController::class, "universitas"])->middleware('auth:api');
    Route::get("sekolah", [InstitusiController::class, "sekolah"])->middleware('auth:api');
    
    Route::get('logbook/peserta/{peserta_id}', [LogbookController::class, 'LogbookByIdPeserta'])->middleware('auth:api');
    Route::get('presensi/peserta/{peserta_id}', [PresensiController::class, 'PresensiByIdPeserta'])->middleware('auth:api');
    Route::get("check", [PresensiController::class, "checkDuplicate"])->middleware('auth:api');
    Route::get('dokumen/peserta/{peserta_id}', [DokumenController::class, 'DokumenByIdPeserta'])->middleware('auth:api');

    Route::get("status", [OptionController::class, "getStatus"])->middleware('auth:api');
    Route::get("nilai", [OptionController::class, "getNilai"])->middleware('auth:api');

    Route::post("sendmail", [DokumenController::class, "sendMail"])->middleware('auth:api');
    Route::post('export-peserta-aktif', [ExportController::class, 'exportPesertaAktif'])->middleware('auth:api');
    Route::post('export-arsip-peserta', [ExportController::class, 'exportArsipPeserta'])->middleware('auth:api');
    Route::get('generate-certificate/{id}', [SertifikatController::class, 'generateCertificate'])->middleware('auth:api');
    Route::get('export-logbook', [ExportController::class, 'exportLogbook'])->middleware('auth:api');
    Route::get('export-presensi', [ExportController::class, 'exportPresensi'])->middleware('auth:api');

    Route::get('kepala-bagian', [KepalaBagianController::class, 'index'])->middleware('auth:api');
    Route::post('kepala-bagian', [KepalaBagianController::class, 'store'])->middleware('auth:api');
    Route::delete('kepala-bagian/{id}', [KepalaBagianController::class, 'destroy'])->middleware('auth:api');

    Route::get('penandatangan', [PenandatanganController::class, 'index'])->middleware('auth:api');
    Route::post('penandatangan', [PenandatanganController::class, 'store'])->middleware('auth:api');

    Route::prefix('dashboard')->group(function () {
        Route::get('scorecard', [DashboardController::class, 'dahboardAdmin']);
        Route::get('peserta-by-institusi', [DashboardController::class, 'distribusiPesertaByInstitusi']);
        Route::get('tingkat-pendidikan', [DashboardController::class, 'distribusiTingkatPendidikan']);
        Route::get('peserta-by-lokasi', [DashboardController::class, 'distribusiPesertaByLokasi']);
        Route::get('peserta-by-status', [DashboardController::class, 'pesertaByStatus']);
        Route::get('presensi-peserta/{id}', [DashboardController::class, 'presensiPeserta']);
        Route::get('logbook-peserta/{id}', [DashboardController::class, 'logbookPeserta']);
        Route::get('administrasi-peserta/{id}', [DashboardController::class, 'administrasiPeserta']);
    });
});
