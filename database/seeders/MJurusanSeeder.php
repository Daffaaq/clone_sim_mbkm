<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MJurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_jurusan')->insert([
            ['jurusan_id' => 1, 'jurusan_code' => 'TI', 'jurusan_name' => 'Teknologi Informasi'],
        ]);
    }
}
