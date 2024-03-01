<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MPeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_periode')->insert([
            [
                'periode_nama' => 'Genap 2023/2024',
                'periode_direktur' => 'Dr. Ir. H. Arief Satria, M.Sc.',
                'periode_nip' => '196509011991031001',
                'is_active' => 1,
                'is_current' => 1
            ],
            [
                'periode_nama' => 'Ganjil 2023/2024',
                'periode_direktur' => 'Dr. Ir. H. Arief Satria, M.Sc.',
                'periode_nip' => '196509011991031001',
                'is_active' => 1,
                'is_current' => 0
            ]
        ]);
    }
}
