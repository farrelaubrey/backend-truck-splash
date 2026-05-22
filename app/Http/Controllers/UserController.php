<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Skenario Gambar 1: Menampilkan Landing Page Utama
     */
    public function landingPage()
    {
        // Menampilkan halaman utama dengan informasi statistik platform
        // Seperti: 500k+ UMKM Terbantu, Rp 4.2T Total Pendanaan, Bunga 1.2%/bulan
        return view('landing_page');
    }

    /**
     * Skenario Gambar 2: Memproses Form Daftar Akun Baru
     */
    public function register(Request $request)
    {
        // 1. Validasi input sesuai kolom yang ada pada form pendaftaran di Gambar 2
        $request->validate([
            'nama_lengkap'          => 'required|string|max:100',
            'email'                 => 'required|email|string|unique:users,email',
            'nomor_telepon'         => 'required|string|max:13',
            'kata_sandi'            => 'required|string|min:8', 
            'konfirmasi_kata_sandi' => 'required|string|same:kata_sandi',
        ], [
            // Skenario Alternatif jika validasi gagal
            'email.unique'      => 'Email sudah terdaftar atau format tidak valid!',
            'kata_sandi.min'    => 'Kata sandi minimal harus 8 karakter!',
            'same'              => 'Konfirmasi kata sandi tidak cocok dengan kata sandi asli.',
        ]);

        // 2. Mapping data dan simpan ke dalam database (Tabel Users)
        User::create([
            'id_user'       => 'user' . rand(1004, 9999), // Format otomatis dari dokumen 3NF
            'nama_lengkap'  => $request->nama_lengkap,
            'email'         => $request->email,
            'nomor_telpon'  => $request->nomor_telepon,
            'password'      => Hash::make($request->kata_sandi), // Enkripsi kata sandi
            'status_akun'   => 'Aktif' // Status default user baru
        ]);

        // 3. Mengarahkan kembali ke halaman login dengan notifikasi sukses
        return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan masuk kembali.');
    }

    /**
     * Skenario Gambar 3: Memproses Form Login (Masuk Sekarang)
     */
    public function login(Request $request)
    {
        // 1. Validasi kolom input email dan password yang ada di Gambar 3
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
            'role_type' => 'required|string|in:user,admin' // Membaca tab aktif (User Login / Admin Login)
        ]);

        // Mengambil kredensial login
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password
        ];

        // 2. Mencoba proses autentikasi ke sistem
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Skenario Alternatif: Mencegah user login jika status akun dibekukan/nonaktif
            if ($user->status_akun === 'Terbeku' || $user->status_akun === 'Nonaktif') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akses ditolak. Akun Anda sedang ditangguhkan!']);
            }

            // 3. Mengarahkan dashboard berdasarkan tipe login yang dipilih di tab UI
            if ($request->role_type === 'admin') {
                return redirect()->intended('/admin/dashboard')
                                 ->with('success', 'Selamat Datang Admin Panel!');
            }

            return redirect()->intended('/dashboard')
                             ->with('success', 'Selamat Datang Kembali, ' . $user->nama_lengkap);
        }

        // Skenario Alternatif jika kombinasi email atau password salah
        return back()->withErrors([
            'email' => 'Email atau password salah. Silakan periksa kembali akun Anda.',
        ])->onlyInput('email');
    }

    /**
     * Alur tambahan untuk menghancurkan session ketika logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}