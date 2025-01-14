<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mahasiswa;
use App\Models\Siswa;
use App\Models\Status;
use App\Models\Lokasi;
use Carbon\Carbon;

class UpdatePesertaStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-peserta-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status peserta dari aktif ke selesai berdasarkan tangga_selesai';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $today = Carbon::now()->toDateString();
        $statusAktif = Status::where("name", "Aktif")->first();
        $statusSelesai = Status::where("name", "Selesai")->first();

        if (!$statusAktif || !$statusSelesai) {
            $this->error('Status "Aktif" atau "Selesai" tidak ditemukan.');
            return;
        }
        
        $mahasiswa = Mahasiswa::where('tanggal_selesai', $today)
            ->where('status_id', $statusAktif->id)
            ->get(); 
        
        foreach ($mahasiswa as $mhs) {
            // Update status mahasiswa
            $mhs->update(['status_id' => $statusSelesai->id]);

            // Update Kuota_Terisi 
            $lokasi = Lokasi::find($mhs->lokasi_id);
            if ($lokasi) {
                $lokasi->kuota_terisi -= 1;
                $lokasi->save();
            }
        }
        
        $siswa = Siswa::where('tanggal_selesai', $today)
            ->where('status_id', $statusAktif->id)
            ->get();
        
        foreach ($siswa as $sis) {
            // Update status siswa
            $sis->update(['status_id' => $statusSelesai->id]);

            // Update Kuota_Terisi
            $lokasi = Lokasi::find($sis->lokasi_id);
            if ($lokasi) {
                $lokasi->kuota_terisi -= 1;
                $lokasi->save();
            }
        }

        $this->info('Status Peserta telah diperbarui.');
    }
}
