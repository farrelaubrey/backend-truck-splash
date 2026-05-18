<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validasi sesuai kebutuhan tabel users di laporan
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nomor_telpon' => 'required|max:13'
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nomor_telpon' => $request->nomor_telpon,
            'status_akun' => 'Aktif' // Status default
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil!');
    }
}