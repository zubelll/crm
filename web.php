<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\CutiReminderController;
use App\Http\Controllers\KontakController;

// Auth
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// Protected
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/mahasiswa', MahasiswaController::class);
    Route::get('/mahasiswa/status/{status}', [MahasiswaController::class, 'byStatus']);

    Route::resource('/pengajuan', PengajuanController::class);
    Route::patch('/pengajuan/{id}/status', [PengajuanController::class, 'updateStatus']);

    Route::get('/cuti',           [CutiReminderController::class, 'index'])->name('cuti.index');
    Route::post('/cuti/{id}/notif', [CutiReminderController::class, 'kirimNotif'])->name('cuti.notif');
    Route::patch('/cuti/{id}/aktif',[CutiReminderController::class, 'aktifkanKembali'])->name('cuti.aktif');

    Route::resource('/kontak', KontakController::class);
    Route::get('/kontak/{id}/wa-link', [KontakController::class, 'generateLink']);
});

