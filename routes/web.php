<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DummyController;
use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\Profile\MahasiswaProfileController;
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\ProfileController;
use App\Http\Controllers\Transaction\InstrukturController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::pattern('INV', '[A-Z0-9]{12}'); // Filter Parameter INVOICE
Route::pattern('theme', '[a-z]+'); // Filter Parameter INVOICE
Route::pattern('id', '[0-9]+'); // Filter Parameter GET
Route::pattern('detail_id', '[0-9]+'); // Filter Parameter GET
Route::pattern('sub_detail_id', '[0-9]+'); // Filter Parameter GET
Route::pattern('uuid', '[A-Fa-f0-9]{32}$'); // Filter Parameter GET
Route::pattern('date', '[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])'); // Filter Parameter GET
Route::get('/verify-email/{token}', [InstrukturController::class, 'verifyEmail'])->name('verify.email');

Auth::routes(['register' => false, 'confirm' => false, 'email' => false, 'reset' => false]);
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register_perusahaan']);
Route::post('/register_perusahaan', [App\Http\Controllers\Auth\RegisterController::class, 'register_perusahaan_store'])->name('register.perusahaan');
Route::get('/login-sso', [App\Http\Controllers\Auth\LoginController::class, 'showLoginSSO']);
Route::get('/attempt-sso', [App\Http\Controllers\Auth\LoginController::class, 'attemptLoginSSO']);

Route::get('/', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/home', [DashboardController::class, 'index'])->middleware('auth');

// theme
Route::get('/theme/{theme}', [\App\Http\Controllers\Setting\ThemeController::class, 'index']);

Route::group(['prefix' => 'setting', 'middleware' => ['auth']], function () {
    // profile
    Route::get('account', [AccountController::class, 'index']);
    Route::put('account', [AccountController::class, 'update']);
    Route::put('account/avatar', [AccountController::class, 'update_avatar']);
    Route::put('account/password', [AccountController::class, 'update_password']);

    // profile
    Route::get('profile', [ProfileController::class, 'index']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('profile/avatar', [ProfileController::class, 'update_avatar']);
    Route::put('profile/password', [ProfileController::class, 'update_password']);
});

Route::group(['prefix' => 'mahasiswa', 'middleware' => ['auth']], function () {
    Route::get('profile', [MahasiswaProfileController::class, 'index']);
    Route::put('profile', [MahasiswaProfileController::class, 'update']);
    Route::put('profile/avatar', [MahasiswaProfileController::class, 'update_avatar']);
    Route::put('profile/password', [MahasiswaProfileController::class, 'update_password']);
});

Route::post('berita', [DashboardController::class, 'berita']);
Route::get('berita/{uid}', [DashboardController::class, 'berita_detail']);

Route::get('kota', [KabupatenController::class, 'getKota']);

Route::resource('dummy', DummyController::class);
Route::post('dummy/list', [DummyController::class, 'list']);
