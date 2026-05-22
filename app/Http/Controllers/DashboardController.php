<?php

namespace App\Http\Controllers;

use App\Models\LimitPinjaman;
use App\Models\PembayaranTagihan;
use App\Models\PaketPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil data limit pinjaman milik user yang sedang login
        $limit = LimitPinjaman::where('id_user', $userId)->first();

        // 2. Ambil satu tagihan terdekat yang statusnya belum dibayar
        $tagihanTerdekat = PembayaranTagihan::whereHas('pengajuanPinjaman', function($query) use ($userId) {
            $query->where('id_user', $userId);
        })
        ->where('status_pembayaran', 'Belum Bayar')
        ->orderBy('jatuh_tempo', 'asc')
        ->first();

        // 3. Ambil semua paket pinjaman yang tersedia untuk ditampilkan di dashboard
        $paketTersedia = PaketPinjaman::all();

        // Kirim semua data ke view dashboard peminjam
        return view('peminjam.dashboard', compact('limit', 'tagihanTerdekat', 'paketTersedia'));
    }
}