<?php

use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\Profile\DosenProfileController;
use App\Http\Controllers\Proposal\DosenLogBimbinganController;
use App\Http\Controllers\Proposal\DosenPembimbingController;
use App\Http\Controllers\Proposal\DosenRekapUjianSemproController;
use App\Http\Controllers\Proposal\DosenUjianSemproController;
use App\Http\Controllers\Proposal\DosenUsulanTopikController;
use App\Http\Controllers\Proposal\DosenSemproController;
use App\Http\Controllers\Setting\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'lecturer', 'middleware' => ['auth']], function() {

    // profile Dosen
    Route::group(['prefix' => 'profile', 'middleware' => ['auth']], function() {
        Route::get('/', [DosenProfileController::class, 'index']);
        Route::put('/', [DosenProfileController::class, 'update']);
        Route::put('avatar', [DosenProfileController::class, 'update_avatar']);
        Route::put('password', [DosenProfileController::class, 'update_password']);
    });
});

Route::group(['prefix' => 'proposal', 'middleware' => ['auth']], function() {

    // Proposal
    Route::resource('usulan-topik', DosenUsulanTopikController::class)->parameter('usulan-topik', 'id');
    Route::post('usulan-topik/list', [DosenUsulanTopikController::class, 'list']);
    Route::get('usulan-topik/{id}/delete', [DosenUsulanTopikController::class, 'confirm']);
    Route::put('usulan-topik/{id}/respon', [DosenUsulanTopikController::class, 'respon_usulan']);

    Route::get('pembimbing', [DosenPembimbingController::class, 'index']);
    Route::post('pembimbing/list', [DosenPembimbingController::class, 'list']);
    Route::get('pembimbing/{uuid}', [DosenPembimbingController::class, 'show']);
    Route::put('pembimbing/{uuid}', [DosenPembimbingController::class, 'response_pembimbing']);

    Route::get('bimbingan', [DosenLogBimbinganController::class, 'index']);
    Route::post('bimbingan/list', [DosenLogBimbinganController::class, 'list']);
    Route::get('bimbingan/{uuid}', [DosenLogBimbinganController::class, 'show']);
    Route::put('bimbingan/{uuid}', [DosenLogBimbinganController::class, 'response_pembimbing']);

    // Pendaftaran Seminar Proposal
    Route::get('sempro', [DosenSemproController::class, 'index']);
    Route::post('sempro/list', [DosenSemproController::class, 'list']);
    Route::get('sempro/{uuid}', [DosenSemproController::class, 'show']);
    Route::put('sempro/{uuid}', [DosenSemproController::class, 'response_pembimbing']);
    Route::post('sempro/dosen-pendamping', [DosenSemproController::class, 'dosen_pendamping']);
    Route::post('sempro/dosen-pembahas', [DosenSemproController::class, 'dosen_pembahas']);


    // Ujian Seminar Proposal
    Route::get('ujian-sempro', [DosenUjianSemproController::class, 'index']);
    Route::post('ujian-sempro/list', [DosenUjianSemproController::class, 'list']);
    Route::get('ujian-sempro/{uuid}', [DosenUjianSemproController::class, 'show']);
    Route::put('ujian-sempro/{uuid}', [DosenUjianSemproController::class, 'update']);

    // Rekap Ujian Seminar Proposal
    Route::get('rekap-ujian-sempro', [DosenRekapUjianSemproController::class, 'index']);
    Route::get('rekap-ujian-sempro/{id}/pembimbing', [DosenRekapUjianSemproController::class, 'ba_pembimbing']);
    Route::get('rekap-ujian-sempro/{id}/penguji-1', [DosenRekapUjianSemproController::class, 'ba_penguji_1']);
    Route::get('rekap-ujian-sempro/{id}/penguji-2', [DosenRekapUjianSemproController::class, 'ba_penguji_2']);
});
