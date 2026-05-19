<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanPinjaman; 

class PengajuanController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'jumlah_pinjaman' => 'required|numeric',
            'tenor' => 'required|integer',
            'bukti_ktp' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        // 2. Simpan Data ke Database
        PengajuanPinjaman::create([
            'id_user' => auth()->id(), // Mengambil ID user yang login
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'tenor' => $request->tenor,
            'status_pengajuan' => 'Menunggu', // Status default sesuai laporan
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil dikirim!');
    }
}