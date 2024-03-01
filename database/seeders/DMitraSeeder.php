<?php

namespace Database\Seeders;

use App\Models\MitraModel;
use Illuminate\Database\Seeder;

class DMitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MitraModel::insert([
            [
                'mitra_id' => 1,
                'kegiatan_id' => 1,
                'periode_id' => 1,
                'mitra_prodi' => json_encode([1, 2]),
                'mitra_nama' => 'PT IMSS',
                'mitra_alamat' => 'Kota Madiun',
                'mitra_website' => 'https://imsservice.co.id',
                'mitra_deskripsi' => 'test',
                'mitra_durasi' => 4,
                'provinsi_id' => 11,
                'kota_id' => 140,
                'status' => 1,
                'mitra_keterangan_ditolak' => NULL
            ], [
                'mitra_id' => 2,
                'kegiatan_id' => 2,
                'periode_id' => 1,
                'mitra_prodi' => json_encode([1, 2]),
                'mitra_nama' => 'PT Rekaindo Global Jasa',
                'mitra_alamat' => 'Kota Madiun',
                'mitra_website' => 'https://ptrekaindo.co.id',
                'mitra_deskripsi' => 'test',
                'mitra_durasi' => 5,
                'provinsi_id' => 11,
                'kota_id' => 140,
                'status' => 0,
                'mitra_keterangan_ditolak' => NULL
            ], [
                'mitra_id' => 3,
                'kegiatan_id' => 8,
                'periode_id' => 1,
                'mitra_prodi' => json_encode([2]),
                'mitra_nama' => 'PT INKA (Persero)',
                'mitra_alamat' => 'Kota Madiun',
                'mitra_website' => 'https://inka.co.id',
                'mitra_deskripsi' => 'test',
                'mitra_durasi' => 3,
                'provinsi_id' => 11,
                'kota_id' => 140,
                'status' => 2,
                'mitra_keterangan_ditolak' => 'Fiktif'
            ],
        ]);
    }
}
