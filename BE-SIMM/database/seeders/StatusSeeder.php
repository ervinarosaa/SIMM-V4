<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list_status = [
            ['nama_status' => 'Aktif'],
            ['nama_status' => 'Selesai'],
            ['nama_status' => 'Batal'],
        ];

        foreach ($list_status as $status) {
            Status::create($status);
        }
    }
}
