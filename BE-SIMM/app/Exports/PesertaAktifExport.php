<?php

namespace App\Exports;

use App\Models\Peserta;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class PesertaAktifExport implements FromCollection
{
    protected $startDate;
    protected $endDate;

    // Menerima rentang waktu (start_date dan end_date)
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect([
            ["Laporan Data Magang Peserta Aktif"],
            [
                "Nama Lengkap", 
                "NIS", 
                "Nomor Telepon", 
                "Institusi", 
                "Fakultas", 
                "Jurusan", 
                "Pembimbing", 
                "Tanggal Mulai", 
                "Tanggal Selesai", 
                "Lokasi", 
                "Status"
            ],
        ]);

        $statusAktif = Status::where("nama_status", "Aktif")->first();
        
        $peserta = Peserta::with("lokasi", "institusi", "status")
            ->where("id_status", $statusAktif->id)
            ->whereBetween("tanggal_mulai", [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($peserta) {
                return [
                    "Nama Lengkap" => $peserta->nama_peserta,
                    "NIS" => "'" . $peserta->nomor_induk,
                    "Nomor Telepon" => "'" . $peserta->nomor_telepon ?? "-",
                    "Institusi" => $peserta->institusi->nama_institusi ?? "N/A",
                    "Fakultas" => $peserta->fakultas,
                    "Jurusan" => $peserta->jurusan,
                    "Pembimbing" => $peserta->nama_pembimbing,
                    "Tanggal Mulai" => $peserta->tanggal_mulai,
                    "Tanggal Selesai" => $peserta->tanggal_selesai,
                    "Lokasi" => $peserta->lokasi->nama_lokasi ?? "N/A",
                    "Status" => $peserta->status->nama_status,
                ];
            });
        
        return $data->merge($peserta);
    }
}
