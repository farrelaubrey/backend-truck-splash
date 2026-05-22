<?php

namespace App\Http\Controllers;

use App\Models\PaketPinjaman;
use App\Models\BisnisUmkm;
use App\Models\PengajuanPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    /**
     * Tampilan Awal: Daftar Paket Pinjaman 
     */
    public function index(Request $request)
    {
        $query = PaketPinjaman::query();

        // Fitur Filter Kategori Bisnis (Semua, Kuliner, Warung, Jasa)
        if ($request->has('kategori') && $request->kategori != 'Semua Kategori') {
            $query->where('sektor_kategori', $request->kategori);
        }

        // Fitur Sortir (Terbaru, Bunga Rendah, Tenor)
        if ($request->sortir == 'Bunga Rendah') {
            $query->orderBy('bunga_per_bulan', 'asc');
        } elseif ($request->sortir == 'Tenor') {
            $query->orderBy('tenor_max', 'desc');
        } else {
            $query->orderBy('created_at', 'desc'); // Default Terbaru
        }

        $paketPinjaman = $query->get();

        return view('peminjam.paket_pinjaman', compact('paketPinjaman'));
    }

    /**
     *  Menyimpan Langkah 1 (Informasi Dasar Bisnis)
     */
    public function storeLangkah1(Request $request)
    {
        $request->validate([
            'nama_bisnis'     => 'required|string|max:100',
            'kategori_bisnis' => 'required|string',
            'alamat_lengkap'  => 'required|string',
            'lama_beroperasi' => 'required|string',
        ]);

        $bisnis = BisnisUmkm::updateOrCreate(
            ['id_user' => Auth::id()],
            [
                'id_bisnis'       => 'bisnis' . rand(3004, 9999),
                'nama_bisnis'     => $request->nama_bisnis,
                'kategori_bisnis' => $request->kategori_bisnis,
                'alamat_lengkap'  => $request->alamat_lengkap,
                'lama_beroperasi' => $request->lama_beroperasi,
            ]
        );

        // Alihkan ke halaman Langkah 2 (Upload Dokumen)
        return redirect()->route('pengajuan.langkah2');
    }

    /**
     *  Menyimpan Langkah 2 (Upload Berkas Legalitas, Keuangan & Jaminan)
     */
    public function storeLangkah2(Request $request)
    {
        $request->validate([
            'bukti_ktp_diri'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_tempat_usaha'  => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'surat_izin_usaha'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'laporan_arus_kas'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pendapatan_bulanan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'rekening_koran'     => 'required|file|mimes:pdf|max:4096',
            // Field jaminan di bawah bersifat opsional sesuai keterangan Gambar 4
            'foto_aset'          => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'stnk_bpkb'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Proses penyimpanan file ke storage root
        $paths = [];
        foreach ($request->files as $key => $file) {
            if ($request->hasFile($key)) {
                $paths[$key] = $request->file($key)->store('dokumen_pengajuan');
            }
        }

        // Simpan data berkas sementara ke session sebelum konfirmasi final di langkah 3
        session(['dokumen_paths' => $paths]);

        return redirect()->route('pengajuan.langkah3');
    }

    /**
     *  Menampilkan Langkah 3 (Ringkasan Analisis Keuangan & Simulasi Cicilan)
     */
    public function tampilLangkah3()
    {
        // Contoh kalkulasi statis berbasis data Gambar 5 (Plafon Rp 10.000.000, Tenor 12 Bulan)
        $plafon = 10000000;
        $tenor = 12;
        $bungaPersen = 12; // 12% per tahun
        
        $biayaAdmin = 100000;
        $totalDiterima = $plafon - $biayaAdmin;

        $angsuranPokok = round($plafon / $tenor);
        $angsuranBunga = round(($plafon * ($bungaPersen / 100)) / $tenor);
        $totalCicilan = $angsuranPokok + $angsuranBunga;

        return view('peminjam.langkah3', compact(
            'plafon', 'tenor', 'bungaPersen', 'biayaAdmin', 
            'totalDiterima', 'angsuranPokok', 'angsuranBunga', 'totalCicilan'
        ));
    }

    /**
     * Gambar 5: Aksi Final Klik Tombol "AJUKAN SEKARANG"
     */
    public function submitFinal(Request $request)
    {
        $dokumen = session('dokumen_paths');

        if (!$dokumen) {
            return redirect()->route('pengajuan.langkah2')->with('error', 'Dokumen gagal diproses, silahkan upload kembali.');
        }

        // Buat record data transaksi pengajuan pinjaman final ke database
        PengajuanPinjaman::create([
            'id_pinjaman'          => 'pinjam' . rand(4004, 9999),
            'id_user'              => Auth::id(),
            'jumlah_pinjaman'      => 10000000, // Berdasarkan nominal deal simulasi
            'tenor'                => 12,
            'status_pengajuan'     => 'Menunggu',
            'bukti_ktp_diri'       => $dokumen['bukti_ktp_diri'] ?? null,
            'foto_tempat_usaha'    => $dokumen['foto_tempat_usaha'] ?? null,
            'surat_izin_usaha'     => $dokumen['surat_izin_usaha'] ?? null,
            'laporan_arus_kas'     => $dokumen['laporan_arus_kas'] ?? null,
            'pendapatan_bulanan'   => $dokumen['pendapatan_bulanan'] ?? null,
            'rekening_koran'       => $dokumen['rekening_koran'] ?? null,
            'foto_aset'            => $dokumen['foto_aset'] ?? null,
            'stnk_bpkb'            => $dokumen['stnk_bpkb'] ?? null,
            'id_admin_verifikator' => null,
        ]);

        // Hapus temp session storage
        session()->forget('dokumen_paths');

        return redirect('/pengajuan-berhasil');
    }
}