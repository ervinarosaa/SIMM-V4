<?php

namespace App\Exports;

use App\Models\Logbook;
use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LogbookExport implements FromCollection, WithDrawings
{
    protected $startDate;
    protected $endDate;
    protected $id_peserta;
    protected $images = [];

    public function __construct($startDate, $endDate, $id_peserta = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->id_peserta = $id_peserta;
    }

    /**
     * Mengumpulkan data untuk diekspor ke Excel
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->id_peserta) {
            // Export logbook untuk peserta tertentu
            $peserta = Peserta::with("institusi", "lokasi", "logbook")->find($this->id_peserta);

            $data = collect([
                ["Logbook Magang"],
                [""],
                ["Nama Lengkap", ":". $peserta->nama_peserta],
                ["NIS", ":", $peserta->nomor_induk],
                ["Fakultas", ":", $peserta->fakultas ?? "-"],
                ["Jurusan", ":", $peserta->jurusan],
                ["Institusi", ":", $peserta->institusi->nama_institusi],
                ["Lokasi Magang", ":", $peserta->lokasi->nama_lokasi],
                [
                    "No",
                    "Tanggal",
                    "Title",
                    "Kegiatan",
                    "Dokumentasi"
                ],
            ]);

            $logbook = $peserta->logbook
                ->map(function ($logbook, $index) {
                    // Menyimpan data gambar untuk digunakan di drawings()
                    $imagePath = public_path('storage/logbook/' . basename($logbook->dokumentasi));
                    if (file_exists($imagePath)) {
                        $this->images[] = [
                            'path' => $imagePath,
                            'coordinates' => 'E' . ($index + 10), 
                        ];
                    }
                    return [
                        "No" => $index + 1,
                        "Tanggal" => $logbook->tanggal_logbook,
                        "Title" => $logbook->title,
                        "Kegiatan" => $logbook->deskripsi,
                        "Dokumentasi" => null, 
                    ];
                });
        } else {
            // Export logbook berdasarkan rentang tanggal
            $data = collect([ 
                ["Logbook Magang"],
                ["Periode: " . $this->startDate . " s/d " . $this->endDate],
                [
                    "No",
                    "Tanggal",
                    "Nama Peserta",
                    "NIS",
                    "Institusi",
                    "Lokasi Magang",
                    "Title",
                    "Kegiatan",
                    "Dokumentasi"
                ],
            ]);

            $logbook = Logbook::with("peserta.lokasi", "peserta.institusi")
                ->whereBetween("tanggal_logbook", [$this->startDate, $this->endDate])
                ->get()
                ->map(function ($logbook, $index) {
                    $imagePath = public_path('storage/logbook/' . basename($logbook->dokumentasi));
                    if (file_exists($imagePath)) {
                        $this->images[] = [
                            'path' => $imagePath,
                            'coordinates' => 'I' . ($index + 4),
                        ];
                    }
                    return [
                        "No" => $index + 1,
                        "Tanggal" => $logbook->tanggal_logbook,
                        "Nama Peserta" => $logbook->peserta->nama_peserta,
                        "NIS/NPM" => $logbook->peserta->nomor_induk,
                        "Institusi" => $logbook->peserta->institusi->nama_institusi ?? "N/A",
                        "Lokasi Magang" => $logbook->peserta->lokasi->nama_lokasi ?? "N/A",
                        "Title" => $logbook->title,
                        "Kegiatan" => $logbook->deskripsi,
                        "Dokumentasi" => null,
                    ];
                });
        }

        return $data->merge($logbook);
    }

    /**
     * Menambahkan gambar ke file Excel
     * 
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\Drawing[]
     */
    public function drawings()
    {
        $drawings = [];

        foreach ($this->images as $image) {
            $drawing = new Drawing();
            $drawing->setName('Image');
            $drawing->setDescription('Logbook Image');
            $drawing->setPath($image['path']); // Path gambar
            $drawing->setHeight(80); // Tinggi gambar
            $drawing->setCoordinates($image['coordinates']); // Kolom dan baris gambar
            $drawing->setOffsetX(10); // Mengatur posisi gambar horizontal
            $drawing->setOffsetY(5);  // Mengatur posisi gambar vertikal
            $drawings[] = $drawing;
        }

        return $drawings;
    }
}
