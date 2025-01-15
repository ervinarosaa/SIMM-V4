<?php

namespace App\Exports;

use App\Models\Presensi;
use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PresensiExport implements FromCollection
{
    protected $startDate;
    protected $endDate;
    protected $id_peserta;

    public function __construct($startDate, $endDate, $id_peserta = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->id_peserta = $id_peserta;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->id_peserta) {
            $peserta = Peserta::with("institusi", "lokasi", "presensi")->find($this->id_peserta);
            
            $data = collect([ 
                ["Presensi Magang"],
                [""],
                ["Nama Lengkap", ":", $peserta->nama_peserta],
                ["NIS/NIM", ":", $peserta->nomor_induk],
                ["Fakultas", ":", $peserta->fakultas ?? "-"],
                ["Jurusan", ":", $peserta->jurusan],
                ["Institusi", ":", $peserta->institusi->nama_institusi],
                ["Lokasi Magang", ":", $peserta->lokasi->nama_lokasi],
                [
                    "No",
                    "Tanggal",
                    "Keterangan",
                    "Geolocation",
                ],
            ]);

            $presensi = $peserta->presensi
                ->map(function ($presensi, $index) {
                    $geolocationUrl = $presensi->latitude_presensi && $presensi->longitude_presensi 
                        ? "https://www.google.com/maps?q={$presensi->latitude_presensi},{$presensi->longitude_presensi}"
                        : "Tidak Tersedia";
                    return [
                        "No" => $index + 1,
                        "Tanggal" => $presensi->tanggal_presensi,
                        "Keterangan" => $presensi->keterangan_presensi,
                        "Geolocation" => $geolocationUrl,
                    ];
                });
        }else {
            $data = collect([ 
                ["Presensi Magang"],
                ["Periode: " . $this->startDate . " s/d " . $this->endDate],
                [
                    "No",
                    "Tanggal",
                    "Nama Peserta",
                    "NIS/NPM",
                    "Institusi",
                    "Lokasi Magang",
                    "Keterangan",
                    "Geolocation",
                ],
            ]);

            $presensi = Presensi::with("peserta.lokasi", "peserta.institusi")
                ->whereBetween("tanggal_presensi", [
                    $this->startDate, 
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->get()
                ->map(function ($presensi, $index) {
                    $geolocationUrl = $presensi->latitude_presensi && $presensi->longitude_presensi 
                        ? "https://www.google.com/maps?q={$presensi->latitude_presensi},{$presensi->longitude_presensi}"
                        : "Tidak Tersedia";
                    return [
                        "No" => $index + 1,
                        "Tanggal" => $presensi->tanggal_presensi,
                        "Nama Peserta" => $presensi->peserta->nama_peserta,
                        "NIS/NIM" => $presensi->peserta->nomor_induk,
                        "Institusi" => $presensi->peserta->institusi->nama_institusi ?? "N/A",
                        "Lokasi Magang" => $presensi->peserta->lokasi->nama_lokasi ?? "N/A",
                        "Keterangan" => $presensi->keterangan_presensi,
                        "Geolocation" => $geolocationUrl,
                    ];
                });
        }

        return $data->merge($presensi);
    }
}
