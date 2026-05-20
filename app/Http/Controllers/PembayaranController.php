<?php

namespace App\Http\Controllers;

use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /**
     * Menyimpan/Menerbitkan data tagihan baru (Oleh Admin).
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            // id_tagihan dihapus dari sini jika di database diset sebagai Auto Increment
            'id_pinjaman'    => 'required|exists:pengajuan_pinjaman,id', // Sesuaikan nama primary key di tabel pengajuan (biasanya 'id' atau 'id_pinjaman')
            'jumlah_tagihan' => 'required|numeric|min:1000',
            'jatuh_tempo'    => 'required|date|after_or_equal:today', // Memastikan tanggal jatuh tempo tidak lewat dari hari ini
        ]);

        // 2. Simpan data ke tabel pembayaran_tagihan
        PembayaranTagihan::create([
            // 'id_tagihan' tidak perlu diisi jika Auto Increment
            'id_pinjaman'       => $request->id_pinjaman,
            'jumlah_tagihan'    => $request->jumlah_tagihan,
            'jatuh_tempo'       => $request->jatuh_tempo,
            'status_pembayaran' => 'Belum Bayar' // Status awal tagihan baru
        ]);

        return redirect()->back()->with('success', 'Tagihan baru berhasil dibuat!');
    }

    /**
     * Mengkonfirmasi pembayaran menjadi Lunas (Saat user bayar / admin verifikasi).
     */
    public function bayar($id)
    {
        // Mencari data tagihan berdasarkan primary key
        $tagihan = PembayaranTagihan::findOrFail($id);
        
        // Update status menjadi Lunas
        $tagihan->update([
            'status_pembayaran' => 'Lunas'
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi dan status diperbarui menjadi Lunas!');
    }
}