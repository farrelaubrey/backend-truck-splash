<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanPinjaman; 

class PengajuanController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input (Menambahkan bukti_aset wajib berupa gambar)
        $request->validate([
            'jumlah_pinjaman' => 'required|numeric|min:100000', // Minimal pinjaman, sesuaikan kebutuhan
            'tenor'           => 'required|integer|min:1',
            'bukti_ktp'       => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
            'bukti_aset'      => 'required|image|mimes:jpeg,png,jpg|max:2048', // Tambahan Bukti Aset
        ]);

        // 2. Proses Upload File Gambar ke Folder Storage
        // File akan disimpan di folder: storage/app/public/bukti_pengajuan
        $pathKtp  = $request->file('bukti_ktp')->store('bukti_pengajuan', 'public');
        $pathAset = $request->file('bukti_aset')->store('bukti_pengajuan', 'public');

        // 3. Simpan Data ke Database
        PengajuanPinjaman::create([
            'id_user'          => auth()->id(), 
            'jumlah_pinjaman'  => $request->jumlah_pinjaman,
            'tenor'            => $request->tenor,
            'bukti_ktp'        => $pathKtp,  // Menyimpan nama/path file KTP yang sukses diupload
            'bukti_aset'       => $pathAset, // Menyimpan nama/path file Aset yang sukses diupload
            'status_pengajuan' => 'Menunggu', 
        ]);

        return redirect()->back()->with('success', 'Pengajuan dan bukti aset berhasil dikirim!');
    }
}