<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPinjaman;
use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PinjamanSayaController extends Controller
{
    /**
     *  Menampilkan Halaman "Pinjaman Saya"
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Hitung data akumulasi untuk komponen Banner Atas
        // Total Pinjaman Aktif (Status Pengajuan 'Disetujui')
        $totalPinjamanAktif = PengajuanPinjaman::where('id_user', $userId)
            ->where('status_pengajuan', 'Disetujui')
            ->sum('jumlah_pinjaman');

        // Total Sisa Tagihan yang statusnya belum lunas
        $totalSisaTagihan = PembayaranTagihan::whereHas('pengajuanPinjaman', function($query) use ($userId) {
                $query->where('id_user', $userId);
            })
            ->where('status_pembayaran', 'Belum Bayar')
            ->sum('jumlah_tagihan');

        // 2. Ambil daftar Pinjaman Aktif beserta data relasi cicilan terbarunya
        $pinjamanAktif = PengajuanPinjaman::where('id_user', $userId)
            ->where('status_pengajuan', 'Disetujui')
            ->with(['tagihan' => function($query) {
                $query->where('status_pembayaran', 'Belum Bayar')->orderBy('jatuh_tempo', 'asc');
            }])
            ->get();

        // 3. Ambil daftar Status Pengajuan yang belum disetujui (misal: Menunggu Verifikasi / Sedang Ditinjau)
        $statusPengajuan = PengajuanPinjaman::where('id_user', $userId)
            ->whereIn('status_pengajuan', ['Menunggu', 'Direview'])
            ->get();

        return view('peminjam.pinjaman_saya', compact(
            'totalPinjamanAktif', 
            'totalSisaTagihan', 
            'pinjamanAktif', 
            'statusPengajuan'
        ));
    }

    /**
     *  Menyimpan data unggahan dari Pop-up Form Konfirmasi Pembayaran Angsuran
     */
    public function kirimBuktiPembayaran(Request $request, $id_tagihan)
    {
        // 1. Validasi field form konfirmasi pembayaran sesuai struktur di Gambar 2
        $request->validate([
            'rekening_tujuan'  => 'required|string',
            'nominal_transfer' => 'required|numeric|min:0',
            'tanggal_transfer' => 'required|date',
            'bukti_transfer'   => 'required|image|mimes:jpg,jpeg,png|max:2048', // Batas 2MB format JPG/PNG/JPEG
        ], [
            'required' => 'Kolom ini wajib diisi/diunggah!',
            'image'    => 'Bukti transfer harus berupa gambar (JPG, PNG, JPEG)!'
        ]);

        // 2. Cari data tagihan bulanan yang dimaksud
        $tagihan = PembayaranTagihan::findOrFail($id_tagihan);

        // 3. Upload file bukti transfer (struk/screenshot) ke direktori lokal storage
        if ($request->hasFile('bukti_transfer')) {
            $pathFile = $request->file('bukti_transfer')->store('bukti_pembayaran_tagihan');
        }

        // 4. Update data transaksi tagihan dengan data transfer manual
        $tagihan->update([
            'rekening_tujuan'  => $request->rekening_tujuan,
            'nominal_transfer' => $request->nominal_transfer,
            'tanggal_transfer' => $request->tanggal_transfer,
            'bukti_transfer'   => $pathFile ?? null,
            'status_pembayaran'=> 'Menunggu Verifikasi', // Status berubah agar diverifikasi oleh Admin di backend
        ]);

        // 5. Redirect kembali ke menu Pinjaman Saya dengan pesan sukses
        return redirect()->route('pinjaman-saya.index')
            ->with('success', 'Bukti pembayaran angsuran berhasil dikirim! Menunggu verifikasi admin.');
    }
}