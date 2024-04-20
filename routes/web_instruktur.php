<?php

use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\Master\PerusahaanController;
use App\Http\Controllers\Setting\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'instruktur', 'middleware' => ['auth']], function () {



    // Route::get('profile', [MahasiswaProfileController::class, 'index']);
    // Route::put('profile', [MahasiswaProfileController::class, 'update']);
});

Route::resource('kegiatan', KegiatanController::class)->parameter('kegiatan', 'id');
Route::post('kegiatan/list', [KegiatanController::class, 'list']);
Route::get('kegiatan/{id}/delete', [KegiatanController::class, 'confirm']);
