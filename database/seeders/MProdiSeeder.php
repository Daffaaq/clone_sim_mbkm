<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_prodi')->insert([
            ['prodi_id' => 1, 'jurusan_id' => 1, 'prodi_code' => 'D4TI', 'prodi_name' => 'Teknik Informatika', 'is_active' => 1],
            ['prodi_id' => 2, 'jurusan_id' => 1, 'prodi_code' => 'D4SIB', 'prodi_name' => 'Sistem Informasi Bisnis', 'is_active' => 1],
            ['prodi_id' => 3, 'jurusan_id' => 1, 'prodi_code' => 'D2PPLS', 'prodi_name' => 'Pengembangan Piranti Lunak Situs', 'is_active' => 1],
        ]);
    }
}
