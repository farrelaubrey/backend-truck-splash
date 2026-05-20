<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranTagihan extends Model
{
    use HasFactory;

    // 1. Sesuaikan nama tabel di database Anda
    protected $table = 'pembayaran_tagihan';

    // 2. Tentukan Primary Key jika namanya bukan 'id'
    protected $primaryKey = 'id_tagihan';

    // 3. Matikan incrementing jika id_tagihan Anda BUKAN berupa angka integer otomatis (misal: string/UUID)
    // public $incrementing = false;
    // protected $keyType = 'string';

    // 4. Daftarkan kolom yang boleh diisi massal
    protected $fillable = [
        'id_tagihan', // Masukkan ini jika id_tagihan diinput manual (bukan auto-increment)
        'id_pinjaman',
        'jumlah_tagihan',
        'jatuh_tempo',
        'status_pembayaran',
    ];

    /**
     * Relasi ke model PengajuanPinjaman.
     * Mengasumsikan setiap tagihan terikat pada satu pengajuan pinjaman.
     */
    public function pengajuanPinjaman()
    {
        // 'id_pinjaman' adalah foreign key yang ada di tabel pembayaran_tagihan
        return $this->belongsTo(PengajuanPinjaman::class, 'id_pinjaman');
    }
}