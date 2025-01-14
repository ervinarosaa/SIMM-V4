<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TingkatPendidikan;

class TingkatPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list_tingkat = [
            ['nama_tingkat' => 'Perguruan Tinggi'],
            ['nama_tingkat' => 'Sekolah Kejuruan'],
        ];

        foreach ($list_tingkat as $tingkat) {
            TingkatPendidikan::create($tingkat);
        }
    }
}
