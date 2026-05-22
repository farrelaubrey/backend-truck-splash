<?php

namespace App\Http\Controllers;

use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Memproses form pop-up konfirmasi pembayaran angsuran dari sisi Peminjam
     */
    public function konfirmasiPembayaran(Request $request, $id_tagihan)
    {
        $request->validate([
            'rekening_tujuan'  => 'required|string',
            'nominal_transfer' => 'required|numeric|min:0',
            'tanggal_transfer' => 'required|date',
            'bukti_transfer'   => 'required|image|mimes:jpg,jpeg,png|max:2048', 
        ], [
            'required' => 'Kolom ini wajib diisi / diunggah.',
            'image'    => 'Bukti transfer harus berupa gambar (JPG, JPEG, PNG).',
            'max'      => 'Ukuran file bukti transfer maksimal 2MB.'
        ]);

        $tagihan = PembayaranTagihan::findOrFail($id_tagihan);

        // Proses simpan file struk dari peminjam
        if ($request->hasFile('bukti_transfer')) {
            if ($tagihan->bukti_transfer) {
                Storage::delete($tagihan->bukti_transfer);
            }
            $pathFile = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }

        // Update data tagihan milik peminjam menjadi 'Menunggu Verifikasi'
        $tagihan->update([
            'rekening_tujuan'  => $request->rekening_tujuan,
            'nominal_transfer' => $request->nominal_transfer,
            'tanggal_transfer' => $request->tanggal_transfer,
            'bukti_transfer'   => $pathFile ?? null,
            'status_pembayaran'=> 'Menunggu Verifikasi', // Mengirim data ke antrean admin
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi oleh admin.');
    }
}