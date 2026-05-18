<?php

use Illuminate\Support\Facades\Route;
// Import semua controller yang sudah dibuat tadi
use App\Http\Controllers\UserController;
use App\Http\Controllers\BisnisController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PembayaranController;

Route::get('/', function () {
    return view('welcome');
});

// --- ROUTES UNTUK SPLASH TRUCK ---

// 1. Route untuk User (Dashboard & Profil)
Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

// 2. Route untuk Bisnis UMKM (Simpan data usaha)
Route::post('/bisnis/simpan', [BisnisController::class, 'store'])->name('bisnis.store');

// 3. Route untuk Pengajuan Pinjaman
Route::get('/pinjaman/ajukan', [PengajuanController::class, 'create'])->name('pinjaman.create'); // Tampilan form
Route::post('/pinjaman/kirim', [PengajuanController::class, 'store'])->name('pinjaman.store');   // Proses simpan ke DB

// 4. Route untuk Pembayaran Tagihan
Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');