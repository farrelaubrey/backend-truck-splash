<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPinjaman extends Model
{
    use HasFactory;

    // 1. Tentukan nama tabel jika nama tabel di database Anda bukan "pengajuan_pinjamans"
    protected $table = 'pengajuan_pinjaman'; 

    // 2. Daftarkan semua kolom agar bisa diisi menggunakan metode ::create()
    protected $fillable = [
        'id_user',
        'jumlah_pinjaman',
        'tenor',
        'bukti_ktp',
        'bukti_aset', // Kolom tambahan baru
        'status_pengajuan',
    ];

    /**
     * Relasi ke model User.
     * Mengasumsikan setiap pengajuan dimiliki oleh satu pengguna (User).
     */
    public function user()
    {
        // Parameter kedua ('id_user') adalah nama foreign key di tabel pengajuan Anda
        return $this->belongsTo(User::class, 'id_user');
    }
}