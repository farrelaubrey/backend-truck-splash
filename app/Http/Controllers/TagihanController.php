<?php

namespace App\Http\Controllers;

use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    /**
     * Mengambil rincian angsuran spesifik dalam bentuk JSON untuk komponen Pop-up (Gambar 2)
     */
    public function rincianJson($id_tagihan)
    {
        // Cari data tagihan berdasarkan ID
        $tagihan = PembayaranTagihan::find($id_tagihan);

        if (!$tagihan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tagihan tidak ditemukan'
            ], 404);
        }

        // Kembalikan data struktur sesuai dengan teks tampilan Pop-up Gambar 2
        return response()->json([
            'id_tagihan'     => $tagihan->id_tagihan, // #INV-10027-TS
            'cicilan_pokok'  => $tagihan->cicilan_pokok,
            'biaya_layanan'  => $tagihan->biaya_layanan,
            'denda'          => $tagihan->denda_keterlambatan,
            'periode_display'=> 'Bulan Ke-' . $tagihan->periode_ke . ' / ' . $tagihan->total_periode,
            'total_tagihan'  => $tagihan->total_tagihan,
            'jatuh_tempo'    => $tagihan->jatuh_tempo
        ]);
    }

    /**
     * Aksi ketika menekan tombol "BAYAR SEKARANG" di dashboard atau komponen tagihan
     */
    public function bayar(Request $request, $id_tagihan)
    {
        $tagihan = PembayaranTagihan::findOrFail($id_tagihan);
        
        // Integrasi sistem pembayaran (Misal Midtrans/Xendit Virtual Account) bisa ditaruh di sini
        
        return redirect('/pembayaran/proses/' . $tagihan->id_tagihan);
    }
}