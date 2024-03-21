<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // for Super Admin and Admin
        DB::table('s_menu')->upsert([
            ['menu_id' => '1', 'menu_scope' => 'ALL', 'menu_code' => 'DASHBOARD', 'menu_name' => 'Dashboard', 'menu_url' => '/', 'menu_level' => '1', 'order_no' => '1', 'parent_id' => NULL, 'class_tag' => 'dashboard', 'icon' => 'fas fa-tachometer-alt', 'is_active' => '1'],
            ['menu_id' => '2', 'menu_scope' => 'ADMIN', 'menu_code' => 'MASTER', 'menu_name' => 'Data Master', 'menu_url' => NULL, 'menu_level' => '1', 'order_no' => '3', 'parent_id' => NULL, 'class_tag' => 'master', 'icon' => 'fas fa-th', 'is_active' => '1'],
            ['menu_id' => '3', 'menu_scope' => 'ADMIN', 'menu_code' => 'TRANSACTION', 'menu_name' => 'Transaksi', 'menu_url' => NULL, 'menu_level' => '1', 'order_no' => '4', 'parent_id' => NULL, 'class_tag' => 'transaction', 'icon' => 'fas fa-edit', 'is_active' => '1'],
            ['menu_id' => '4', 'menu_scope' => 'ADMIN', 'menu_code' => 'REPORT', 'menu_name' => 'Laporan', 'menu_url' => NULL, 'menu_level' => '1', 'order_no' => '5', 'parent_id' => NULL, 'class_tag' => 'report', 'icon' => 'fas fa-file-invoice', 'is_active' => '1'],
            ['menu_id' => '5', 'menu_scope' => 'SUPER', 'menu_code' => 'SETTING', 'menu_name' => 'Setting', 'menu_url' => NULL, 'menu_level' => '1', 'order_no' => '6', 'parent_id' => NULL, 'class_tag' => 'setting', 'icon' => 'fas fa-cogs', 'is_active' => '1'],
            ['menu_id' => '6', 'menu_scope' => 'ADMIN', 'menu_code' => 'MASTER.JURUSAN', 'menu_name' => 'Jurusan', 'menu_url' => 'master/jurusan', 'menu_level' => '2', 'order_no' => '1', 'parent_id' => '2', 'class_tag' => 'master-jurusan', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '7', 'menu_scope' => 'ADMIN', 'menu_code' => 'MASTER.PRODI', 'menu_name' => 'Program Studi', 'menu_url' => 'master/prodi', 'menu_level' => '2', 'order_no' => '2', 'parent_id' => '2', 'class_tag' => 'master-prodi', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '8', 'menu_scope' => 'ADMIN', 'menu_code' => 'MASTER.PROGRAM', 'menu_name' => 'Program', 'menu_url' => 'master/program', 'menu_level' => '2', 'order_no' => '3', 'parent_id' => '2', 'class_tag' => 'master-program', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '9', 'menu_scope' => 'ADMIN', 'menu_code' => 'MASTER.KEGIATAN', 'menu_name' => 'Kegiatan', 'menu_url' => 'master/kegiatan', 'menu_level' => '2', 'order_no' => '4', 'parent_id' => '2', 'class_tag' => 'master-kegiatan', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '10', 'menu_scope' => 'ADMIN', 'menu_code' => 'MASTER.PERIODE', 'menu_name' => 'Periode', 'menu_url' => 'master/periode', 'menu_level' => '2', 'order_no' => '5', 'parent_id' => '2', 'class_tag' => 'master-periode', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '11', 'menu_scope' => 'ALL', 'menu_code' => 'TRANSACTION.MITRA', 'menu_name' => 'Mitra MBKM', 'menu_url' => 'transaksi/mitra', 'menu_level' => '2', 'order_no' => '1', 'parent_id' => '3', 'class_tag' => 'transaksi-mitra', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '12', 'menu_scope' => 'ALL', 'menu_code' => 'TRANSACTION.MAHASISWA', 'menu_name' => 'Mahasiswa', 'menu_url' => 'transaksi/mahasiswa', 'menu_level' => '2', 'order_no' => '2', 'parent_id' => '3', 'class_tag' => 'transaksi-mahasiswa', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '13', 'menu_scope' => 'KOORDINATOR-ADMIN', 'menu_code' => 'TRANSACTION.PENDAFTARAN', 'menu_name' => 'Pendaftaran MBKM', 'menu_url' => 'transaksi/pendaftaran', 'menu_level' => '2', 'order_no' => '3', 'parent_id' => '3', 'class_tag' => 'transaksi-pendaftaran', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '14', 'menu_scope' => 'MAHASISWA', 'menu_code' => 'TRANSACTION.DAFTAR.MAGANG', 'menu_name' => 'Daftar Magang', 'menu_url' => 'transaksi/daftar-magang', 'menu_level' => '2', 'order_no' => '4', 'parent_id' => '3', 'class_tag' => 'transaksi-daftar-magang', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '15', 'menu_scope' => 'MAHASISWA', 'menu_code' => 'TRANSACTION.PERSETUJUAN.KELOMPOK', 'menu_name' => 'Persetujuan Kelompok', 'menu_url' => 'transaksi/persetujuan-kelompok', 'menu_level' => '2', 'order_no' => '4', 'parent_id' => '3', 'class_tag' => 'transaksi-persetujuan-kelompok', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '16', 'menu_scope' => 'ADMIN', 'menu_code' => 'SETTING.MENU', 'menu_name' => 'Menu', 'menu_url' => 'setting/menu', 'menu_level' => '2', 'order_no' => '1', 'parent_id' => '5', 'class_tag' => 'setting-menu', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '17', 'menu_scope' => 'ADMIN', 'menu_code' => 'SETTING.GROUP', 'menu_name' => 'Group', 'menu_url' => 'setting/group', 'menu_level' => '2', 'order_no' => '2', 'parent_id' => '5', 'class_tag' => 'setting-group', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '18', 'menu_scope' => 'ADMIN', 'menu_code' => 'SETTING.USER', 'menu_name' => 'User', 'menu_url' => 'setting/user', 'menu_level' => '2', 'order_no' => '3', 'parent_id' => '5', 'class_tag' => 'setting-user', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '19', 'menu_scope' => 'ALL', 'menu_code' => 'SETTING.ACCOUNT', 'menu_name' => 'Account', 'menu_url' => 'setting/account', 'menu_level' => '2', 'order_no' => '4', 'parent_id' => '5', 'class_tag' => 'setting-account', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '20', 'menu_scope' => 'ADMIN', 'menu_code' => 'MASTER.DOSEN', 'menu_name' => 'Dosen', 'menu_url' => 'master/dosen', 'menu_level' => '2', 'order_no' => '24', 'parent_id' => '2', 'class_tag' => 'master-dosen', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '21', 'menu_scope' => 'ADMIN', 'menu_code' => 'TRANSACTION.PEMBIMBING.DOSEN', 'menu_name' => 'Pembimbing Dosen', 'menu_url' => 'transaksi/pembimbing-dosen', 'menu_level' => '2', 'order_no' => '4', 'parent_id' => '3', 'class_tag' => 'transaksi-pembimbing-dosen', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],
            ['menu_id' => '22', 'menu_scope' => 'ALL', 'menu_code' => 'TRANSACTION.INSTRUKTUR', 'menu_name' => 'Instruktur', 'menu_url' => 'transaksi/instruktur', 'menu_level' => '2', 'order_no' => '4', 'parent_id' => '3', 'class_tag' => 'transaksi-instruktur', 'icon' => 'fas fa-minus text-xs', 'is_active' => '1'],

        ], ['menu_id', 'menu_code'], ['menu_scope', 'menu_name', 'menu_url', 'class_tag']);
    }
}
