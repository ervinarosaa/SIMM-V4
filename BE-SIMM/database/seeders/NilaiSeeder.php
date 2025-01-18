<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Nilai;

class NilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list_nilai = [
            ['predikat_nilai' => 'Sangat Baik'],
            ['predikat_nilai' => 'Baik'],
            ['predikat_nilai' => 'Cukup'],
            ['predikat_nilai' => 'Kurang'],
        ];

        foreach ($list_nilai as $nilai) {
            Nilai::create($nilai);
        }
    }
}
