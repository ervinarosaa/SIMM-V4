<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KeteranganPresensi;

class KeteranganPresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list_keterangan = [
            ['nama_keterangan' => 'Hadir'],
            ['nama_keterangan' => 'Izin'],
            ['nama_keterangan' => 'Sakit'],
        ];

        foreach ($list_keterangan as $keterangan) {
            KeteranganPresensi::create($keterangan);
        }
    }
}
