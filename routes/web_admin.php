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
use App\Http\Controllers\Report\DaftarMitraController;
use App\Http\Controllers\Report\LogActivityController;
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\GroupController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\ProfileController;
use App\Http\Controllers\Setting\UserController;
use App\Http\Controllers\SuratPengantarController;
use App\Http\Controllers\Transaction\BeritaController as TransactionBeritaController;
use App\Http\Controllers\Transaction\DaftarMagangController;
use App\Http\Controllers\Transaction\DosenPenilaianMahasiswaController;
use App\Http\Controllers\Transaction\InstrukturPenilaianMahasiswaController;
use App\Http\Controllers\Transaction\JadwalDosenPembahasController;
use App\Http\Controllers\Transaction\JadwalDosenPembimbingController;
use App\Http\Controllers\Transaction\JadwalInstrukturLapanganController;
use App\Http\Controllers\Transaction\JadwalSidangMagangController;
use App\Http\Controllers\Transaction\LihatStatusPendaftaranController;
use App\Http\Controllers\Transaction\LihatStatusPengajuanController;
use App\Http\Controllers\Transaction\LogBimbinganController;
use App\Http\Controllers\Transaction\LogBimbinganDosenController;
use App\Http\Controllers\Transaction\LogBimbinganInstrukturController;
use App\Http\Controllers\Transaction\MyBimbinganController;
use App\Http\Controllers\Transaction\MyMagangController;
use App\Http\Controllers\Transaction\NilaiInstrukturLapanganController;
use App\Http\Controllers\Transaction\NilaiPembahasDosenController;
use App\Http\Controllers\Transaction\NilaiPembimbingDosenController;
use App\Http\Controllers\Transaction\PembimbingDosenController;
use App\Http\Controllers\Transaction\PersetujuanKelompokController;
use App\Http\Controllers\Transaction\QuotaDosenController;
use App\Http\Controllers\Transaction\SemhasController;
use App\Http\Controllers\Transaction\SemhasDaftarController;
use App\Http\Controllers\Transaction\UjianSeminarHasilController;
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
    Route::get('mahasiswa/import', [MahasiswaController::class, 'import']);
    Route::post('mahasiswa/import', [MahasiswaController::class, 'import_action']);

    //dosen
    Route::resource('dosen', DosenController::class)->parameter('dosen', 'id');
    Route::post('dosen/list', [DosenController::class, 'list']);
    Route::get('dosen/{id}/delete', [DosenController::class, 'confirm']);
    Route::post('dosen/import', [DosenController::class, 'import_action'])->name('dosen.import');

    Route::resource('my-magang', MyMagangController::class)->parameter('my-magang', 'id');
    Route::post('my-magang/list', [MyMagangController::class, 'list']);
    Route::get('my-magang/{id}/delete', [MyMagangController::class, 'confirm']);

    Route::resource('seminar-hasil', SemhasController::class)->parameter('semhas', 'id');
    Route::post('seminar-hasil/list', [SemhasController::class, 'list']);
    Route::get('seminar-hasil/{id}/delete', [SemhasController::class, 'confirm']);

    Route::resource('seminarhasil-daftar', SemhasDaftarController::class)->parameter('semhas', 'id');
    Route::post('seminarhasil-daftar/list', [SemhasDaftarController::class, 'list']);
    Route::get('seminarhasil-daftar/{id}/delete', [SemhasDaftarController::class, 'confirm']);
    Route::post('seminarhasil/daftar', [SemhasDaftarController::class, 'daftarSemhas'])->name('daftar.semhas');

    //log bimbingan
    Route::resource('log-bimbingan', LogBimbinganController::class)->parameter('log-bimbingan', 'id');
    Route::post('log-bimbingan/list', [LogBimbinganController::class, 'list']);
    Route::get('log-bimbingan/{id}/delete', [LogBimbinganController::class, 'confirm']);
    Route::get('log-bimbingan/cetak_pdf', [LogBimbinganController::class, 'reportLogBimbingan'])->name('cetak.logbimbingan');



    //log bimbingan instrukur
    Route::resource('log-bimbingan-instruktur', LogBimbinganInstrukturController::class)->parameter('log-bimbingan', 'id');
    Route::post('log-bimbingan-instruktur/list', [LogBimbinganInstrukturController::class, 'list']);
    Route::get('log-bimbingan-instruktur/{id}/delete', [LogBimbinganInstrukturController::class, 'confirm']);

    Route::post('log-bimbingan-instruktur/updateinstruktur', [LogBimbinganInstrukturController::class, 'updateStatusInstruktur'])->name('update.logbimbingan.instruktur');
    Route::post('log-bimbingan-instruktur/updateinstruktur{id}', [LogBimbinganInstrukturController::class, 'updateStatusInstrukturFromModal'])->name('update.logbimbingan.instruktur.modal');

    //penilaian Mahasiswa instrukur
    Route::resource('penilaian-mahasiswa-instruktur', InstrukturPenilaianMahasiswaController::class)->parameter('penilaian-mahasiswa-instruktur', 'id');
    Route::post('penilaian-mahasiswa-instruktur/list', [InstrukturPenilaianMahasiswaController::class, 'list']);
    Route::get('penilaian-mahasiswa-instruktur/{id}/delete', [InstrukturPenilaianMahasiswaController::class, 'confirm']);
    Route::post('penilaian-mahasiswa-instruktur/updatedataPenilaianMahasiswa', [InstrukturPenilaianMahasiswaController::class, 'updatedataPenilaianMahasiswa'])->name('update.penilaian.mahasiswa');
    //penilaian Mahasiswa instrukur



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
    Route::get('lihat-status-pendaftaran/{kode}/ganti-anggota', [LihatStatusPendaftaranController::class, 'ganti_anggota']);
    Route::post('lihat-status-pendaftaran/{kode}/ganti-anggota', [LihatStatusPendaftaranController::class, 'ganti_anggota_action']);

    //lihat status
    Route::resource('lihat-status-pengajuan', LihatStatusPengajuanController::class)->parameter('lihat-status-pengajuan', 'id');
    Route::post('lihat-status-pengajuan/list', [LihatStatusPengajuanController::class, 'list']);
    Route::get('lihat-status-pengajuan/{id}/alasan', [LihatStatusPengajuanController::class, 'alasan']);

    Route::resource('pembimbing-dosen', PembimbingDosenController::class)->parameter('pembimbing-dosen', 'id');
    Route::post('pembimbing-dosen/list', [PembimbingDosenController::class, 'list']);

    Route::resource('jadwal-semhas', JadwalSidangMagangController::class)->parameter('jadwal-semhas', 'id');
    Route::post('jadwal-semhas/list', [JadwalSidangMagangController::class, 'list'])->name('jadwal-semhas.list');

    //ujian-seminar-hasil
    Route::resource('ujian-seminar-hasil', UjianSeminarHasilController::class)->parameter('ujian-seminar-hasil', 'id');
    Route::post('ujian-seminar-hasil/berita-acara', [UjianSeminarHasilController::class, 'uploadBeritaAcara'])->name('upload-berita-acara');
    Route::get('ujian-seminar-hasil/{id}/nilai-pembimbing', [UjianSeminarHasilController::class, 'nilai'])->name('nilai-mahasiswa-dosen-pembimbing');
    Route::get('ujian-seminar-hasil/{id}/nilai-pembahas', [UjianSeminarHasilController::class, 'nilaiDosenPembahas'])->name('nilai-mahasiswa-dosen-pembahas');
    Route::get('ujian-seminar-hasil/{id}/nilai-instruktur', [UjianSeminarHasilController::class, 'nilaiInstrukturLapangan'])->name('nilai-mahasiswa-instruktur-lapangan');
    Route::get('ujian-seminar-hasil/{id}/nilai-akhir', [UjianSeminarHasilController::class, 'nilaiAkhir'])->name('nilai-mahasiswa-akhir');

    // jadwal-seminar-hasil intruktur lapangan
    Route::resource('jadwal-semhas-instruktur', JadwalInstrukturLapanganController::class)->parameter('log-bimbingan', 'id');
    Route::post('jadwal-semhas-instruktur/list', [JadwalInstrukturLapanganController::class, 'list']);
    Route::get('jadwal-semhas-instruktur/{id}/nilai', [JadwalInstrukturLapanganController::class, 'nilai']);
    Route::post('jadwal-semhas-instruktur/nilai', [JadwalInstrukturLapanganController::class, 'simpanNilai'])->name('simpan.nilai.instruktur');
    //berita
    Route::resource('berita', TransactionBeritaController::class)->parameter('berita', 'id');
    Route::post('berita/list', [TransactionBeritaController::class, 'list']);
    Route::get('berita/{id}/delete', [TransactionBeritaController::class, 'confirm']);
});

Route::group(['prefix' => 'laporan', 'middleware' => ['auth']], function () {
    Route::resource('daftar-mahasiswa-diterima', DaftarMahasiswaDiterimaController::class)->parameter('daftar-mahasiswa-diterima', 'id');
    Route::post('daftar-mahasiswa-diterima/list', [DaftarMahasiswaDiterimaController::class, 'list']);
    Route::get('daftar-mahasiswa-diterima/{id}/delete', [DaftarMahasiswaDiterimaController::class, 'confirm']);
    Route::get('daftar-mahasiswa/export', [MahasiswaController::class, 'export']);

    Route::resource('daftar-mitra', DaftarMitraController::class)->parameter('daftar-mitra', 'id');
    Route::post('daftar-mitra/list', [DaftarMitraController::class, 'list']);
    Route::get('daftar-mitra/{id}/delete', [DaftarMitraController::class, 'confirm']);
    Route::get('daftar-mitra/export', [MitraController::class, 'export']);
});

Route::resource('daftar-mahasiswa-diterima', DaftarMahasiswaDiterimaController::class)->parameter('daftar-mahasiswa-diterima', 'id');
Route::post('daftar-mahasiswa-diterima/list', [DaftarMahasiswaDiterimaController::class, 'list']);
Route::get('daftar-mahasiswa-diterima/{id}/delete', [DaftarMahasiswaDiterimaController::class, 'confirm']);

//kuota with url mitra/{id}/kuota
Route::prefix('mitra/{id}')->group(function () {
    Route::resource('kuota', MitraKuotaController::class);
    Route::post('kuota/list', [MitraKuotaController::class, 'list']);
    Route::get('kuota/{kuota}/delete', [MitraKuotaController::class, 'confirm']);
});

Route::group(['prefix' => 'dosen-pembimbing', 'middleware' => ['auth']], function () {
    //my bimbingan
    Route::resource('my-bimbingan', MyBimbinganController::class)->parameter('my-bimbingan', 'id');
    Route::post('my-bimbingan/list', [MyBimbinganController::class, 'list']);
    //log bimbingan dosen
    Route::resource('log-bimbingan-dosen', LogBimbinganDosenController::class)->parameter('log-bimbingan', 'id');
    Route::post('log-bimbingan-dosen/list', [LogBimbinganDosenController::class, 'list']);
    Route::get('log-bimbingan-dosen/{id}/delete', [LogBimbinganDosenController::class, 'confirm']);

    Route::post('log-bimbingan-dosen/updatedosen', [LogBimbinganDosenController::class, 'updateStatusDosen'])->name('update.logbimbingan.dosen');
    Route::post('log-bimbingan-dosen/updatedosen{id}', [LogBimbinganDosenController::class, 'updateStatusDosenFromModal'])->name('update.logbimbingan.dosen.modal');

    //penilaian dosen pembimbing
    Route::resource('penilaian-mahasiswa-dosen', DosenPenilaianMahasiswaController::class)->parameter('log-bimbingan', 'id');
    Route::post('penilaian-mahasiswa-dosen/list', [DosenPenilaianMahasiswaController::class, 'list']);
    Route::get('penilaian-mahasiswa-dosen/{id}/delete', [DosenPenilaianMahasiswaController::class, 'confirm']);
    Route::post('penilaian-mahasiswa-dosen/updatedataPenilaianMahasiswa', [DosenPenilaianMahasiswaController::class, 'updatedataPenilaianMahasiswa'])->name('update.penilaian.mahasiswa.dosen');

    Route::resource('jadwal-semhas', JadwalDosenPembimbingController::class)->parameter('log-bimbingan', 'id');
    Route::post('jadwal-semhas/list', [JadwalDosenPembimbingController::class, 'list']);
    Route::get('jadwal-semhas/{id}/nilai', [JadwalDosenPembimbingController::class, 'nilai']);
    Route::post('jadwal-semhas/nilai', [JadwalDosenPembimbingController::class, 'simpanNilai'])->name('simpan.nilai');
    Route::get('jadwal-semhas/{id}/delete', [JadwalDosenPembimbingController::class, 'confirm']);
});
Route::group(['prefix' => 'dosen-pembahas', 'middleware' => ['auth']], function () {

    Route::resource('jadwal-semhas', JadwalDosenPembahasController::class)->parameter('log-bimbingan', 'id');
    Route::post('jadwal-semhas/list', [JadwalDosenPembahasController::class, 'list']);
    Route::get('jadwal-semhas/{id}/nilai', [JadwalDosenPembahasController::class, 'nilai']);
    Route::post('jadwal-semhas/nilai', [JadwalDosenPembahasController::class, 'simpanNilai'])->name('simpan.nilai.pembahas');
    Route::get('jadwal-semhas/{id}/delete', [JadwalDosenPembahasController::class, 'confirm']);
});
Route::group(['prefix' => 'category', 'middleware' => ['auth']], function () {
    //group
    Route::resource('nilai-pembimbing-dosen', NilaiPembimbingDosenController::class)->parameter('nilai-pembimbing-dosen', 'id');
    Route::post('nilai-pembimbing-dosen/list', [NilaiPembimbingDosenController::class, 'list']);
    Route::get('nilai-pembimbing-dosen/{id}/subcategory', [NilaiPembimbingDosenController::class, 'tambah_subcategory']);
    Route::get('nilai-pembimbing-dosen/{id}/subcategory/detail', [NilaiPembimbingDosenController::class, 'showsub']);
    Route::post('nilai-pembimbing-dosen/{id}/subcategory', [NilaiPembimbingDosenController::class, 'tambah_sub_category'])->name('nilai-pembimbing-dosen.tambah_sub_category');
    Route::delete('nilai-pembimbing-dosen/{id}/subcategory/delete', [NilaiPembimbingDosenController::class, 'destroy_sub_category'])->name('delete_sub_category_dosen_pembimbing');
    Route::get('nilai-pembimbing-dosen/{id}/delete', [NilaiPembimbingDosenController::class, 'confirm']);

    Route::resource('nilai-pembahas-dosen', NilaiPembahasDosenController::class)->parameter('nilai-pembahas-dosen', 'id');
    Route::post('nilai-pembahas-dosen/list', [NilaiPembahasDosenController::class, 'list']);
    Route::get('nilai-pembahas-dosen/{id}/subcategory', [NilaiPembahasDosenController::class, 'tambah_subcategory']);
    Route::get('nilai-pembahas-dosen/{id}/subcategory/detail', [NilaiPembahasDosenController::class, 'showsub']);
    Route::post('nilai-pembahas-dosen/{id}/subcategory', [NilaiPembahasDosenController::class, 'tambah_sub_category'])->name('nilai-pembahas-dosen.tambah_sub_category');
    Route::delete('nilai-pembahas-dosen/{id}/subcategory/delete', [NilaiPembahasDosenController::class, 'destroy_sub_category'])->name('delete_sub_category_dosen_pembahas');
    Route::get('nilai-pembahas-dosen/{id}/delete', [NilaiPembahasDosenController::class, 'confirm']);

    Route::resource('nilai-instruktur-lapangan', NilaiInstrukturLapanganController::class)->parameter('nilai-instruktur-lapangan', 'id');
    Route::post('nilai-instruktur-lapangan/list', [NilaiInstrukturLapanganController::class, 'list']);
    Route::get('nilai-instruktur-lapangan/{id}/subcategory', [NilaiInstrukturLapanganController::class, 'tambah_subcategory']);
    Route::get('nilai-instruktur-lapangan/{id}/subcategory/detail', [NilaiInstrukturLapanganController::class, 'showsub']);
    Route::post('nilai-instruktur-lapangan/{id}/subcategory', [NilaiInstrukturLapanganController::class, 'tambah_sub_category'])->name('nilai-instruktur-lapangan.tambah_sub_category');
    Route::delete('nilai-instruktur-lapangan/{id}/subcategory/delete', [NilaiInstrukturLapanganController::class, 'destroy_sub_category'])->name('delete_sub_category_instruktur-lapangan');
    Route::get('nilai-instruktur-lapangan/{id}/delete', [NilaiInstrukturLapanganController::class, 'confirm']);
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
