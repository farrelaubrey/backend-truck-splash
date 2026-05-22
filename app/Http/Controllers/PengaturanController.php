<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BisnisUmkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    /**
     *  Menampilkan Halaman Utama Menu Pengaturan
     */
    public function index()
    {
        $user = Auth::user();
        // Mengambil data entitas bisnis pendukung milik user terkait
        $bisnis = BisnisUmkm::where('id_user', $user->id_user)->first();

        return view('peminjam.pengaturan', compact('user', 'bisnis'));
    }

    /**
     *  Memproses Form Edit Profil (Simpan Perubahan)
     */
    public function updateProfil(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        // Validasi kolom isian form sesuai komponen pada Gambar 3
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'nomor_telpon' => 'required|string|max:15',
            'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Batas validasi gambar maks 2MB
        ], [
            'required' => 'Kolom ini tidak boleh dikosongkan!',
            'max'      => 'Ukuran file foto profil maksimal berukuran 2MB!'
        ]);

        // Logic memproses unggahan foto profil baru
        if ($request->hasFile('foto_profil')) {
            // Hapus foto profil lama dari server storage jika ada untuk menghemat ruang disk
            if ($user->foto_profil) {
                Storage::delete($user->foto_profil);
            }
            // Simpan foto profil baru ke direktori internal public
            $pathFoto = $request->file('foto_profil')->store('foto_profil_user', 'public');
            $user->foto_profil = $pathFoto;
        }

        // Perbarui sisa kolom teks identitas diri pemilik usaha
        $user->nama_lengkap = $request->nama_lengkap;
        $user->email = $request->email;
        $user->nomor_telpon = $request->nomor_telpon;
        $user->save();

        return redirect()->route('pengaturan.index')->with('success', 'Profil identitas pribadi Anda berhasil diperbarui.');
    }

    /**
     * Tombol Aksi: Edit Entitas Detail Operasional Bisnis (Gambar 2)
     */
    public function updateBisnis(Request $request)
    {
        $request->validate([
            'nama_bisnis'     => 'required|string|max:100',
            'kategori_bisnis' => 'required|string',
            'lokasi'          => 'required|string'
        ]);

        $bisnis = BisnisUmkm::where('id_user', Auth::id())->firstOrFail();
        $bisnis->update([
            'nama_bisnis'     => $request->nama_bisnis,
            'kategori_bisnis' => $request->kategori_bisnis,
            'alamat_lengkap'  => $request->lokasi // Mapping lokasi operasional kota ke alamat database
        ]);

        return redirect()->route('pengaturan.index')->with('success', 'Detail entitas data bisnis berhasil diperbarui.');
    }
}