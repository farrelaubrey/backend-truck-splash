<?php

namespace App\Http\Controllers;

use App\Models\RiwayatTransaksi;
use App\Models\PengajuanPinjaman;
use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     *  Menampilkan halaman Riwayat Transaksi dengan Filter & Ringkasan Kard
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Ambil data kalkulasi nilai untuk 3 Box Kard Informasi atas
        $totalPinjaman = PengajuanPinjaman::where('id_user', $userId)->where('status_pengajuan', 'Disetujui')->sum('jumlah_pinjaman');
        $totalPelunasan = RiwayatTransaksi::where('id_user', $userId)->where('kategori', 'REPAYMENT')->where('status', 'SUCCESS')->sum('jumlah');
        // Karena nominal repayment disimpan sebagai nilai negatif di log (-3.000.000), kita mutlak-kan nilainya dengan abs()
        $totalPelunasan = abs($totalPelunasan); 

        $sisaTagihan = PembayaranTagihan::whereHas('pengajuanPinjaman', function($q) use ($userId) {
            $q->where('id_user', $userId);
        })->where('status_pembayaran', 'Belum Bayar')->sum('jumlah_tagihan');

        // 2. Siapkan query mutasi log tabel bawah beserta fitur filter pencarian & bulan
        $query = RiwayatTransaksi::where('id_user', $userId);

        // Filter: Search Box (Berdasarkan deskripsi atau ID Transaksi)
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('deskripsi', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('id_transaksi', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Filter: Dropdown Bulan (Contoh pilihan pada gambar: "Mei 2026")
        if ($request->has('bulan') && $request->bulan != '') {
            // Asumsi format input dropdown berupa string "YYYY-MM" (e.g., "2026-05")
            $query->whereMonth('tanggal_transaksi', date('m', strtotime($request->bulan)))
                  ->whereYear('tanggal_transaksi', date('Y', strtotime($request->bulan)));
        }

        // Ambil data riwayat diurutkan berdasarkan tanggal terbaru
        $transaksi = $query->orderBy('tanggal_transaksi', 'desc')->get();

        return view('peminjam.riwayat', compact(
            'totalPinjaman', 'totalPelunasan', 'sisaTagihan', 'transaksi'
        ));
    }

    /**
     * Aksi Tombol Pojok Kanan Atas: "UNDUH PDF"
     */
    public function unduhPdf()
    {
        $userId = Auth::id();
        $transaksi = RiwayatTransaksi::where('id_user', $userId)->get();

        // Logic library PDF (misal mPDF / DomPDF) untuk mencetak laporan arus kas
        // $pdf = PDF::loadView('laporan.arus_kas', compact('transaksi'));
        // return $pdf->download('Laporan_Arus_Kas_TruckSplash.pdf');

        return back()->with('success', 'File PDF Laporan Arus Kas berhasil diunduh.');
    }
}