<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranTagihan extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_tagihan';
    protected $primaryKey = 'id_tagihan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_tagihan',
        'id_pinjaman',
        'jumlah_tagihan',
        'jatuh_tempo',
        'status_pembayaran',
        'cicilan_ke',        // Contoh: 3 (dari 3/4 Bln)
        'total_cicilan',     // Contoh: 4 (dari 3/4 Bln)
        'progress_persen',   // Contoh: 75% atau 10% sesuai bar hijau di Gambar 1
        
        // Field Baru untuk menampung input Form Konfirmasi Pembayaran (Gambar 2)
        'rekening_tujuan',   // Bank Mandiri — 144-00-12345-67
        'nominal_transfer',  // Input nominal dari user
        'tanggal_transfer',  // Input tanggal transfer
        'bukti_transfer',    // Path file struk/screenshot yang diunggah
    ];

    public function pengajuanPinjaman()
    {
        return $this->belongsTo(PengajuanPinjaman::class, 'id_pinjaman', 'id_pinjaman');
    }
}