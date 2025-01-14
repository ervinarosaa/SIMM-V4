<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userAdmin = User::where('email', 'ervinarosaaulia@gmail.com')->first();
        Admin::create([
            'nama_admin' => 'Ervina Rosa Aulia',
            'id_user' => $userAdmin->id,
        ]);
    }
}
