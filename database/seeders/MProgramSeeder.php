<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_program')->insert([
            ['program_id' => 1, 'program_kode' => 'P-790', 'program_nama' => 'MBKM', 'program_deskripsi' => 'Mata Kuliah Berbasis Kampus Merdeka', 'program_bulan' => 6],
            ['program_id' => 2, 'program_kode' => 'P-800', 'program_nama' => 'PKL', 'program_deskripsi' => 'Praktik Kerja Lapangan', 'program_bulan' => 3],

        ]);
    }
}
