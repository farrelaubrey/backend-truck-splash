<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PengajuanPinjaman;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     *  Menampilkan Halaman Dashboard Utama Admin
     */
    public function index()
    {
        // 1. Data Ringkasan Statistik Utama (Kard Atas)
        $totalPenyaluran = 12850000000; // Rp 12,85 M (Data Statis sesuai Mockup)
        $umkmAktif = 1248;
        $pengajuanBaruCount = PengajuanPinjaman::where('status_pengajuan', 'Menunggu')->count();
        $tingkatPelunasan = 98.4; // 98.4%

        // 2. Data Distribusi Kategori (Chart Sektor)
        $distribusiSektor = [
            'KULINER' => 42,
            'WARUNG'  => 32,
            'JASA'    => 12
        ];

        // 3. Ambil data limit antrean verifikasi terkini (Tabel Bawah)
        $antreanTerkini = PengajuanPinjaman::where('status_pengajuan', 'Menunggu')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPenyaluran', 'umkmAktif', 'pengajuanBaruCount', 
            'tingkatPelunasan', 'distribusiSektor', 'antreanTerkini'
        ));
    }

    /**
     *  Aksi Pop-up konfirmasi "Ya, Bekukan Akun" pada Manajemen Data Pengguna
     */
    public function bekukanAkun(Request $request, $id_user)
    {
        $request->validate([
            'alasan_pembekuan' => 'required|string'
        ]);

        $user = User::findOrFail($id_user);
        
        // Ubah status akun user menjadi nonaktif/terbeku
        $user->update([
            'status_akun' => 'Terbeku'
        ]);

        return redirect()->back()->with('success', 'Akun ' . $user->nama_lengkap . ' berhasil dibekukan sementara.');
    }
}