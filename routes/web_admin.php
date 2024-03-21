<?php

use App\Http\Controllers\DokumenController;
use App\Http\Controllers\Master\BeritaController;
use App\Http\Controllers\Master\DosenCircleController;
use App\Http\Controllers\Transaction\DosenController;
use App\Http\Controllers\Transaction\InstrukturController;
use App\Http\Controllers\Master\KegiatanController;
use App\Http\Controllers\Master\JurusanController;
use App\Http\Controllers\Master\KategoriController;
use App\Http\Controllers\Master\KegiatanMahasiswaController;
use App\Http\Controllers\Transaction\MahasiswaController;
use App\Http\Controllers\Transaction\PendaftaranController;
use App\Http\Controllers\Master\PeriodeController;
use App\Http\Controllers\Transaction\MitraController;
use App\Http\Controllers\Master\ProdiController;
use App\Http\Controllers\Master\TahapanProposalController;
use App\Http\Controllers\Master\ProgramController;
use App\Http\Controllers\MitraKuotaController;
use App\Http\Controllers\Proposal\AdminHasilSeminarProposalController;
use App\Http\Controllers\Proposal\AdminPendaftaranSemproController;
use App\Http\Controllers\Proposal\AdminProposalMahasiswaBermasalahController;
use App\Http\Controllers\Proposal\AdminProposalMahasiswaController;
use App\Http\Controllers\Proposal\AdminUsulanTopikController;
use App\Http\Controllers\Report\DaftarMahasiswaDiterimaController;
use App\Http\Controllers\Report\LogActivityController;
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\GroupController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\ProfileController;
use App\Http\Controllers\Setting\UserController;
use App\Http\Controllers\SuratPengantarController;
use App\Http\Controllers\Transaction\BeritaController as TransactionBeritaController;
use App\Http\Controllers\Transaction\DaftarMagangController;
use App\Http\Controllers\Transaction\LihatStatusPendaftaranController;
use App\Http\Controllers\Transaction\LihatStatusPengajuanController;
use App\Http\Controllers\Transaction\PembimbingDosenController;
use App\Http\Controllers\Transaction\PersetujuanKelompokController;
use App\Http\Controllers\Transaction\QuotaDosenController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'master', 'middleware' => ['auth']], function () {

    // Jurusan
    Route::resource('jurusan', JurusanController::class)->parameter('jurusan', 'id');
    Route::post('jurusan/list', [JurusanController::class, 'list']);
    Route::get('jurusan/{id}/delete', [JurusanController::class, 'confirm']);

    // Prodi
    Route::resource('prodi', ProdiController::class)->parameter('prodi', 'id');
    Route::post('prodi/list', [ProdiController::class, 'list']);
    Route::get('prodi/{id}/delete', [ProdiController::class, 'confirm']);


    // Tipe Kegiatan
    Route::resource('program', ProgramController::class)->parameter('program', 'id');
    Route::post('program/list', [ProgramController::class, 'list']);
    Route::get('program/{id}/delete', [ProgramController::class, 'confirm']);

    // Jenis Magang
    // Route::resource('kegiatan', KegiatanController::class)->parameter('kegiatan', 'id');
    Route::post('kegiatan/list', [KegiatanController::class, 'list']);
    Route::get('kegiatan/{id}/delete', [KegiatanController::class, 'confirm']);

    // Periode
    Route::resource('periode', PeriodeController::class)->parameter('periode', 'id');
    Route::post('periode/list', [PeriodeController::class, 'list']);
    Route::get('periode/{id}/delete', [PeriodeController::class, 'confirm']);
    Route::get('periode/{id}/confirm_active', [PeriodeController::class, 'confirm_active']);
    Route::put('periode/{id}/active', [PeriodeController::class, 'set_active']);



    // Kegiatan
    Route::get('perusahaan/{id}/kegiatan', [KegiatanController::class, 'index']);
    Route::post('perusahaan/{id}/kegiatan/list', [KegiatanController::class, 'list']);
    Route::get('perusahaan/{id}/kegiatan/create', [KegiatanController::class, 'create']);
    Route::post('perusahaan/{id}/kegiatan/store', [KegiatanController::class, 'store']);
    Route::get('perusahaan/{id}/kegiatan/{kegiatan_id}/edit', [KegiatanController::class, 'edit']);
    Route::get('perusahaan/{id}/kegiatan/{kegiatan_id}/show', [KegiatanController::class, 'show']);
    Route::put('perusahaan/{id}/kegiatan/{kegiatan_id}/update', [KegiatanController::class, 'update']);
    Route::get('perusahaan/{id}/kegiatan/{kegiatan_id}/delete', [KegiatanController::class, 'confirm']);
    Route::delete('perusahaan/{id}/kegiatan/{kegiatan_id}/destroy', [KegiatanController::class, 'destroy']);
    Route::get('perusahaan/{id}/kegiatan/{kegiatan_id}/confirm_approve', [KegiatanController::class, 'confirm_approve']);
    Route::get('perusahaan/{id}/kegiatan/{kegiatan_id}/confirm_reject', [KegiatanController::class, 'confirm_reject']);
    Route::put('perusahaan/{id}/kegiatan/{kegiatan_id}/approve', [KegiatanController::class, 'approve']);
    Route::put('perusahaan/{id}/kegiatan/{kegiatan_id}/reject', [KegiatanController::class, 'reject']);


    //pendaftaran
    Route::resource('pendaftaran', PendaftaranController::class)->parameter('pendaftaran', 'id');
    Route::post('pendaftaran/list', [PendaftaranController::class, 'list']);
    Route::get('pendaftaran/{id}/delete', [PendaftaranController::class, 'confirm']);
});

Route::group(['prefix' => 'transaksi', 'middleware' => ['auth']], function () {
    // mitra
    Route::resource('mitra', MitraController::class)->parameter('mitra', 'id');
    Route::get('mitra/{encrpyt}/show', [MitraController::class, 'show']);
    Route::post('mitra/list', [MitraController::class, 'list']);
    Route::get('mitra/{id}/delete', [MitraController::class, 'confirm']);
    // Route::get('mitra/{id}/confirm_approve', [MitraController::class, 'confirm_approve']);
    // Route::get('mitra/{id}/confirm_reject', [MitraController::class, 'confirm_reject']);
    Route::put('mitra/{id}/update_status', [MitraController::class, 'update_status'])->name('mitra.update.status');
    // Route::put('mitra/{id}/approve', [MitraController::class, 'approve']);
    // Route::put('mitra/{id}/reject', [MitraController::class, 'reject']);
    Route::put('mitra/{id}/kuota', [MitraController::class, 'set_kuota']);
    Route::get('mitra/{id}/alasan', [MitraController::class, 'alasan']);

    // Mahasiswa
    Route::resource('mahasiswa', MahasiswaController::class)->parameter('mahasiswa', 'id');
    Route::post('mahasiswa/list', [MahasiswaController::class, 'list']);
    Route::get('mahasiswa/{id}/delete', [MahasiswaController::class, 'confirm']);

    //dosen
    Route::resource('dosen', DosenController::class)->parameter('dosen', 'id');
    Route::post('dosen/list', [DosenController::class, 'list']);
    Route::get('dosen/{id}/delete', [DosenController::class, 'confirm']);

    //daftar magang
    Route::resource('daftar-magang', DaftarMagangController::class)->parameter('daftar-magang', 'id');
    Route::post('daftar-magang/list', [DaftarMagangController::class, 'list']);
    Route::get('daftar-magang/ajukan', [DaftarMagangController::class, 'ajukan']);
    Route::post('daftar-magang/ajukan', [DaftarMagangController::class, 'ajukan_action']);
    Route::get('daftar-magang/{encrpyt}/show', [DaftarMagangController::class, 'show']);
    Route::get('daftar-magang/{id}/delete', [DaftarMagangController::class, 'confirm']);
    Route::post('daftar-magang/{id}/daftar', [DaftarMagangController::class, 'daftar']);

    Route::resource('instruktur', InstrukturController::class)->parameter('instruktur', 'id');
    Route::post('instruktur/list', [InstrukturController::class, 'list'])->name('instruktur.list');
    Route::get('instruktur/{encrpyt}', [InstrukturController::class, 'lengkapi']);
    Route::post('instruktur/create_instruktur', [InstrukturController::class, 'create_instruktur'])->name('create_instruktur');

    //pendaftaran (role koordinator)
    Route::resource('pendaftaran', PendaftaranController::class)->parameter('pendaftaran', 'id');
    Route::post('pendaftaran/list', [PendaftaranController::class, 'list']);
    Route::get('pendaftaran/{id}/delete', [PendaftaranController::class, 'confirm_delete']);
    Route::get('pendaftaran/{id}/anggota', [PendaftaranController::class, 'anggota']);
    // Route::get('pendaftaran/{id}/confirm_approve', [PendaftaranController::class, 'confirm_approve']);
    // Route::get('pendaftaran/{id}/confirm_approve', [PendaftaranController::class, 'confirm_approve']);
    Route::get('pendaftaran/{id}/confirm', [PendaftaranController::class, 'confirm']);
    Route::put('pendaftaran/{id}/confirm', [PendaftaranController::class, 'confirm_action']);
    Route::get('pendaftaran/{id}/validasi_proposal', [PendaftaranController::class, 'validasi_proposal']);
    Route::get('pendaftaran/{id}/validasi_surat_balasan', [PendaftaranController::class, 'validasi_surat_balasan']);
    Route::post('pendaftaran/confirm_proposal', [PendaftaranController::class, 'confirm_proposal']);
    Route::post('pendaftaran/confirm_sb', [PendaftaranController::class, 'confirm_sb']);
    // Route::put('pendaftaran/{id}/approve', [PendaftaranController::class, 'approve']);
    // Route::put('pendaftaran/{id}/reject', [PendaftaranController::class, 'reject']);

    //persetujuan kelompok
    Route::resource('persetujuan-kelompok', PersetujuanKelompokController::class)->parameter('persetujuan-kelompok', 'id');
    Route::post('persetujuan-kelompok/list', [PersetujuanKelompokController::class, 'list']);
    Route::get('persetujuan-kelompok/{id}/delete', [PersetujuanKelompokController::class, 'confirm']);
    Route::get('persetujuan-kelompok/{id}/confirm_approve', [PersetujuanKelompokController::class, 'confirm_approve']);
    Route::get('persetujuan-kelompok/{id}/confirm_reject', [PersetujuanKelompokController::class, 'confirm_reject']);
    Route::put('persetujuan-kelompok/{id}/approve', [PersetujuanKelompokController::class, 'approve']);
    Route::put('persetujuan-kelompok/{id}/reject', [PersetujuanKelompokController::class, 'reject']);

    //lihat status
    Route::resource('lihat-status-pendaftaran', LihatStatusPendaftaranController::class)->parameter('lihat-status-pendaftaran', 'id');
    Route::post('lihat-status-pendaftaran/list', [LihatStatusPendaftaranController::class, 'list']);
    Route::get('lihat-status-pendaftaran/{encrpyt}', [LihatStatusPendaftaranController::class, 'lengkapi']);
    Route::post('lihat-status-pendaftaran/{id}/suratbalasan', [LihatStatusPendaftaranController::class, 'suratbalasan']);

    //lihat status
    Route::resource('lihat-status-pengajuan', LihatStatusPengajuanController::class)->parameter('lihat-status-pengajuan', 'id');
    Route::post('lihat-status-pengajuan/list', [LihatStatusPengajuanController::class, 'list']);
    Route::get('lihat-status-pengajuan/{id}/alasan', [LihatStatusPengajuanController::class, 'alasan']);

    Route::resource('pembimbing-dosen', PembimbingDosenController::class)->parameter('pembimbing-dosen', 'id');
    Route::post('pembimbing-dosen/list', [PembimbingDosenController::class, 'list']);



    //berita
    Route::resource('berita', TransactionBeritaController::class)->parameter('berita', 'id');
    Route::post('berita/list', [TransactionBeritaController::class, 'list']);
    Route::get('berita/{id}/delete', [TransactionBeritaController::class, 'confirm']);
});

Route::group(['prefix' => 'laporan', 'middleware' => ['auth']], function () {
    Route::resource('daftar-mahasiswa-diterima', DaftarMahasiswaDiterimaController::class)->parameter('daftar-mahasiswa-diterima', 'id');
    Route::post('daftar-mahasiswa-diterima/list', [DaftarMahasiswaDiterimaController::class, 'list']);
    Route::get('daftar-mahasiswa-diterima/{id}/delete', [DaftarMahasiswaDiterimaController::class, 'confirm']);
});

//kuota with url mitra/{id}/kuota
Route::prefix('mitra/{id}')->group(function () {
    Route::resource('kuota', MitraKuotaController::class);
    Route::post('kuota/list', [MitraKuotaController::class, 'list']);
    Route::get('kuota/{kuota}/delete', [MitraKuotaController::class, 'confirm']);
});

Route::group(['prefix' => 'setting', 'middleware' => ['auth']], function () {
    //group
    Route::resource('group', GroupController::class)->parameter('group', 'id');
    Route::post('group/list', [GroupController::class, 'list']);
    Route::get('group/{id}/delete', [GroupController::class, 'confirm']);
    Route::put('group/{id}/menu', [GroupController::class, 'menu_save']);

    //menu
    Route::resource('menu', MenuController::class)->parameter('menu', 'id');
    Route::post('menu/list', [MenuController::class, 'list']);
    Route::get('menu/{id}/delete', [MenuController::class, 'confirm']);

    //user
    Route::resource('user', UserController::class)->parameter('user', 'id');
    Route::post('user/list', [UserController::class, 'list']);
    Route::get('user/{id}/delete', [UserController::class, 'confirm']);
});

Route::get('mahasiswa/{nim}/cari', [MahasiswaController::class, 'cari']);
Route::post('dokumen/upload_proposal', [DokumenController::class, 'upload_proposal'])->name('dokumen.upload_proposal');
Route::post('dokumen/upload_surat_balasan', [DokumenController::class, 'upload_surat_balasan'])->name('dokumen.upload_surat_balasan');

Route::get('daftar-mahasiswa-diterima/{id}/confirm', [SuratPengantarController::class, 'confirm']);
Route::post('surat_pengantar/generate', [SuratPengantarController::class, 'generate'])->name('generate.surat_pengantar');

Route::get('surat_pengantar/{kode}', [SuratPengantarController::class, 'index']);
