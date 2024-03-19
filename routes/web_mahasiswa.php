<?php

use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\Master\PendaftaranController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\Profile\MahasiswaProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'mahasiswa', 'middleware' => ['auth']], function () {



    Route::get('profile', [MahasiswaProfileController::class, 'index']);
    Route::put('profile', [MahasiswaProfileController::class, 'update']);
});

// Route::resource('m/kegiatan', PerusahaanController::class)->parameter('kegiatan', 'id');
// Route::post('m/kegiatan/list', [PerusahaanController::class, 'list']);

//daftar
Route::get('m/kegiatan/daftar/{id}', [KegiatanController::class, 'daftar']);
Route::post('kegiatan/daftar/{id}', [KegiatanController::class, 'daftar_store']);
Route::post('kegiatan/daftar/{id}/undangan', [KegiatanController::class, 'undangan']);
Route::post('kegiatan/daftar/{id}/tolak', [KegiatanController::class, 'tolak']);
Route::post('kegiatan/daftar/{id}/terima', [KegiatanController::class, 'terima']);

//pendaftaran
Route::get('m/pendaftaran', [PendaftaranController::class, 'index']);
