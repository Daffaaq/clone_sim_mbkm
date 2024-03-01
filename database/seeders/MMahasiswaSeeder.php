<?php

namespace Database\Seeders;

use App\Models\Master\MahasiswaModel;
use Illuminate\Database\Seeder;

class MMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MahasiswaModel::insert([
            [
                'prodi_id' => 1,
                'user_id' => 6,
                'nim' => '2041720177',
                'nama_mahasiswa' => 'Dwi Elok Nuraini',
                'email_mahasiswa' => 'dwielok@admin.com',
                'no_hp' => '08123456789',
                'jenis_kelamin' => 0,
                'kelas' => '4D',
                'nama_ortu' => 'Supri',
                'hp_ortu' => '08123456789',
            ], [
                'prodi_id' => 1,
                'user_id' => 7,
                'nim' => '2041720019',
                'nama_mahasiswa' => 'Krismawati',
                'email_mahasiswa' => 'kris@admin.com',
                'no_hp' => '08123456000',
                'jenis_kelamin' => 0,
                'kelas' => '4D',
                'nama_ortu' => '-',
                'hp_ortu' => '-',
            ], [
                'prodi_id' => 1,
                'user_id' => 8,
                'nim' => '2041720158',
                'nama_mahasiswa' => 'Alda',
                'email_mahasiswa' => 'alda@admin.com',
                'no_hp' => '08123456700',
                'jenis_kelamin' => 0,
                'kelas' => '4D',
                'nama_ortu' => 'Belum',
                'hp_ortu' => '000',
            ], [
                'prodi_id' => 2,
                'user_id' => 9,
                'nim' => '21014658',
                'nama_mahasiswa' => 'Yanto',
                'email_mahasiswa' => 'yanto@admin.com',
                'no_hp' => '000000000',
                'jenis_kelamin' => 1,
                'kelas' => 'D',
                'nama_ortu' => 'Belum',
                'hp_ortu' => '000',
            ]
        ]);
    }
}
