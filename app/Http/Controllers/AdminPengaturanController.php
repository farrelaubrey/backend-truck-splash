<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    /**
     *  Menampilkan Halaman Utama Kelola Akses Admin
     */
    public function index()
    {
        $totalAdmin = Admin::count();
        $adminAktif = Admin::where('status', 'Aktif')->count();
        $perluReview = 3; // Komponen statistik sesuai Gambar 6

        // Ambil daftar seluruh staf operasional admin
        $stafAdmin = Admin::all();

        return view('admin.kelola_akses', compact('totalAdmin', 'adminAktif', 'perluReview', 'stafAdmin'));
    }

    /**
     * Aksi Tombol Hijau: "+ TAMBAH ADMIN BARU"
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nama_staff' => 'required|string|max:100',
            'email'      => 'required|email|unique:admin,email',
            'role'       => 'required|string',
            'password'   => 'required|string|min:6'
        ]);

        Admin::create([
            'id_admin'   => 'TS-ADM-' . sprintf("%03d", rand(5, 999)), // Format otomatis sesuai Gambar 6
            'nama_staff' => $request->nama_staff,
            'email'      => $request->email,
            'role'       => $request->role,
            'password'   => bcrypt($request->password),
            'status'     => 'Offline' // Status default sebelum login
        ]);

        return redirect()->back()->with('success', 'Staf operasional admin baru berhasil didaftarkan.');
    }
}