<?php

namespace App\Http\Controllers;

use App\Models\BisnisUmkm;
use Illuminate\Http\Request;

class BisnisController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_bisnis' => 'required|string|max:100',
            'kategori_bisnis' => 'required',
            'usia_bisnis' => 'required|integer',
            'alamat_lengkap' => 'required'
        ]);

        BisnisUmkm::create([
            'user_id' => auth()->id(), // Relasi ke tabel users
            'nama_bisnis' => $request->nama_bisnis,
            'kategori_bisnis' => $request->kategori_bisnis,
            'usia_bisnis' => $request->usia_bisnis,
            'alamat_lengkap' => $request->alamat_lengkap
        ]);

        return redirect('/dashboard')->with('success', 'Data bisnis berhasil disimpan!');
    }
}