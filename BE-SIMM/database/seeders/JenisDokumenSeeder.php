<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JenisDokumen;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list_jenis = [
            ['nama_jenis' => 'Surat Balasan'],
            ['nama_jenis' => 'Sertifikat'],
            ['nama_jenis' => 'Laporan Magang'],
            ['nama_jenis' => 'Lembar Penilaian'],
        ];

        foreach ($list_jenis as $jenis) {
            JenisDokumen::create($jenis);
        }
    }
}
