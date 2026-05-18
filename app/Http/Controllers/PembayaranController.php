<?php

namespace App\Http\Controllers;

use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /**
     * Menyimpan data pembayaran baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi input sesuai kolom di database
        $request->validate([
            'id_tagihan' => 'required|unique:pembayaran_tagihan,id_tagihan',
            'id_pinjaman' => 'required|exists:pengajuan_pinjaman,id_pinjaman',
            'jumlah_tagihan' => 'required|numeric',
            'jatuh_tempo' => 'required|date',
        ]);

        // 2. Simpan data ke tabel pembayaran_tagihan
        PembayaranTagihan::create([
            'id_tagihan' => $request->id_tagihan, // Contoh: tagihan5001 
            'id_pinjaman' => $request->id_pinjaman,
            'jumlah_tagihan' => $request->jumlah_tagihan,
            'jatuh_tempo' => $request->jatuh_tempo,
            'status_pembayaran' => 'Belum Bayar' // Status awal sesuai laporan 
        ]);

        return redirect()->back()->with('success', 'Tagihan berhasil dibuat!');
    }

    /**
     * Mengupdate status pembayaran menjadi Lunas.
     */
    public function bayar($id)
    {
        $tagihan = PembayaranTagihan::findOrFail($id);
        $tagihan->update(['status_pembayaran' => 'Lunas']);

        return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi!');
    }
}