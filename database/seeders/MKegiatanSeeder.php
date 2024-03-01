<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MKegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_kegiatan')->insert([
            [
                'kegiatan_id' => 1,
                'program_id' => 1,
                'kegiatan_kode' => 'K-415',
                'kegiatan_nama' => 'Magang Pusat',
                'kegiatan_skema' => 'M',
                'kegiatan_deskripsi' => 'Sumber Polinema Pusat',
                'is_kuota' => 1,
                'is_mandiri' => 0,
                'is_submit_proposal' => 0,
            ],
            [
                'kegiatan_id' => 2,
                'program_id' => 1,
                'kegiatan_kode' => 'K-815',
                'kegiatan_nama' => 'MSIB - Studi Independent',
                'kegiatan_skema' => 'S',
                'kegiatan_deskripsi' => 'Sumber Kementrian',
                'is_kuota' => 0,
                'is_mandiri' => 0,
                'is_submit_proposal' => 0,
            ],
            [
                'kegiatan_id' => 3,
                'program_id' => 1,
                'kegiatan_kode' => 'K-791',
                'kegiatan_nama' => 'MSIB - Magang Industri',
                'kegiatan_skema' => 'M',
                'kegiatan_deskripsi' => 'Sumber Kementrian',
                'is_kuota' => 0,
                'is_mandiri' => 0,
                'is_submit_proposal' => 0,
            ],
            [
                'kegiatan_id' => 4,
                'program_id' => 1,
                'kegiatan_kode' => 'K-559',
                'kegiatan_nama' => 'IISMA',
                'kegiatan_skema' => 'S',
                'kegiatan_deskripsi' => 'Sumber Kementrian',
                'is_kuota' => 0,
                'is_mandiri' => 0,
                'is_submit_proposal' => 0,
            ],
            [
                'kegiatan_id' => 5,
                'program_id' => 1,
                'kegiatan_kode' => 'K-718',
                'kegiatan_nama' => 'KWU - Merdeka Belajar',
                'kegiatan_skema' => 'M',
                'kegiatan_deskripsi' => 'Sumber Kementrian',
                'is_kuota' => 0,
                'is_mandiri' => 0,
                'is_submit_proposal' => 0,
            ],
            [
                'kegiatan_id' => 6,
                'program_id' => 1,
                'kegiatan_kode' => 'K-482',
                'kegiatan_nama' => 'KWU - JTI Polinema',
                'kegiatan_skema' => 'M',
                'kegiatan_deskripsi' => 'Sumber Jurusan',
                'is_kuota' => 0,
                'is_mandiri' => 0,
                'is_submit_proposal' => 0,
            ],
            [
                'kegiatan_id' => 7,
                'program_id' => 1,
                'kegiatan_kode' => 'K-180',
                'kegiatan_nama' => 'Magang Mandiri (Jurusan)',
                'kegiatan_skema' => 'M',
                'kegiatan_deskripsi' => 'Sumber Jurusan/Dosen',
                'is_kuota' => 1,
                'is_mandiri' => 1,
                'is_submit_proposal' => 1,
            ],
            [
                'kegiatan_id' => 8,
                'program_id' => 2,
                'kegiatan_kode' => 'K-649',
                'kegiatan_nama' => 'PKL Mandiri',
                'kegiatan_skema' => 'M',
                'kegiatan_deskripsi' => 'Sumber Mandiri/Dosen',
                'is_kuota' => 1,
                'is_mandiri' => 1,
                'is_submit_proposal' => 1,
            ],
        ]);
    }
}
