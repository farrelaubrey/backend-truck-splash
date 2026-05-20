<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validasi input dari form pendaftaran
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'required|email|string|unique:users,email',
            'password'     => 'required|string|min:6|confirmed', // 'confirmed' mewajibkan input 'password_confirmation' di form HTML
            'nomor_telpon' => 'required|string|max:13'
        ]);

        // 2. Simpan data ke database menggunakan Mass Assignment
        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'password'     => Hash::make($request->password), // Enkripsi password demi keamanan
            'nomor_telpon' => $request->nomor_telpon,
            'status_akun'  => 'Aktif' // Status default untuk user baru
        ]);

        // 3. Alihkan halaman ke login dengan pesan sukses
        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}