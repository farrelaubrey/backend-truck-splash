<?php

namespace App\Http\Controllers;

use App\Models\BisnisUmkm;
use Illuminate\Http\Request;

class BisnisController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi input dengan lebih ketat dan aman
        $request->validate([
            'nama_bisnis'     => 'required|string|max:100',
            'kategori_bisnis' => 'required|string|in:Kuliner,Warung,Jasa', // Sesuaikan dengan opsi di aplikasi Anda
            'usia_bisnis'     => 'required|integer|min:0', // Mencegah input angka minus
            'alamat_lengkap'  => 'required|string'
        ]);

        // 2. Simpan ke database
        // Pastikan pengguna sudah login, jika belum auth()->id() akan bernilai null
        BisnisUmkm::create([
            'user_id'         => auth()->id(), // Menghubungkan bisnis dengan user yang login
            'nama_bisnis'     => $request->nama_bisnis,
            'kategori_bisnis' => $request->kategori_bisnis,
            'usia_bisnis'     => $request->usia_bisnis,
            'alamat_lengkap'  => $request->alamat_lengkap
        ]);

        // 3. Redirect ke dashboard dengan flash message
        return redirect('/dashboard')->with('success', 'Data bisnis berhasil disimpan!');
    }
}