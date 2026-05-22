<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPinjaman;
use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminVerifikasiController extends Controller
{
    /**
     *  Menampilkan halaman list Verifikasi Pembayaran Angsuran masuk
     */
    public function listPembayaran()
    {
        // Mengambil tagihan yang statusnya 'Menunggu Verifikasi' (setelah user upload struk)
        $pembayaranMasuk = PembayaranTagihan::where('status_pembayaran', 'Menunggu Verifikasi')->get();

        return view('admin.verifikasi_pembayaran', compact('pembayaranMasuk'));
    }

    /**
     *  Aksi Manajemen Pembayaran (Setujui atau Tolak Bukti Transfer)
     */
    public function prosesPembayaran(Request $request, $id_tagihan)
    {
        $tagihan = PembayaranTagihan::findOrFail($id_tagihan);

        if ($request->action === 'setujui') {
            $tagihan->update([
                'status_pembayaran' => 'Lunas'
            ]);
            
            // Logic tambahan untuk mengurangi sisa tagihan utama atau update limit bisa ditaruh disini
            return redirect()->back()->with('success', 'Pembayaran angsuran berhasil diverifikasi dan dikonfirmasi.');
        } 
        
        // Jika status ditolak oleh admin
        $tagihan->update([
            'status_pembayaran' => 'Belum Bayar', // Dikembalikan ke status awal
            'bukti_transfer'    => null // Struk dihapus/ditolak
        ]);

        return redirect()->back()->with('warning', 'Bukti pembayaran ditolak. Status dikembalikan menjadi Belum Bayar.');
    }

    /**
     *  Menampilkan Daftar Antrean Verifikasi Berkas Baru
     */
    public function listPengajuan()
    {
        $totalMenunggu = PengajuanPinjaman::where('status_pengajuan', 'Menunggu')->count();
        $tinjauanHariIni = 15; // Sesuai Mockup Target: 20
        $rataRataDurasi = '4.2j';

        $antreanVerifikasi = PengajuanPinjaman::where('status_pengajuan', 'Menunggu')->get();

        return view('admin.verifikasi_pengajuan_list', compact(
            'totalMenunggu', 'tinjauanHariIni', 'rataRataDurasi', 'antreanVerifikasi'
        ));
    }

    /**
     *  Menampilkan Lembar Detail "Tinjauan Berkas Mitra"
     */
    public function detailPengajuan($id_pinjaman)
    {
        // Eager load data user dan entitas bisnis terkait untuk lembar tinjauan berkas
        $pengajuan = PengajuanPinjaman::with(['user.bisnis'])->findOrFail($id_pinjaman);

        return view('admin.verifikasi_pengajuan_detail', compact('pengajuan'));
    }

    /**
     *  Memproses Keputusan Akhir Verifikasi Berkas Mitra
     */
    public function keputusanPengajuan(Request $request, $id_pinjaman)
    {
        $request->validate([
            'catatan_verifikasi' => 'nullable|string',
            'action'             => 'required|in:setuju,tolak'
        ]);

        $pengajuan = PengajuanPinjaman::findOrFail($id_pinjaman);

        if ($request->action === 'setuju') {
            $pengajuan->update([
                'status_pengajuan'     => 'Disetujui',
                'id_admin_verifikator' => Auth::guard('admin')->id() // Catat ID Admin yang login
            ]);
            return redirect()->route('admin.pengajuan.list')->with('success', 'Pengajuan pinjaman berhasil disetujui.');
        }

        // Jika ditolak atau minta revisi berkas
        $pengajuan->update([
            'status_pengajuan'     => 'Ditolak',
            'id_admin_verifikator' => Auth::guard('admin')->id()
        ]);

        return redirect()->route('admin.pengajuan.list')->with('danger', 'Pengajuan pinjaman ditolak dengan catatan evaluasi.');
    }
}