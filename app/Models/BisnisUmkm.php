<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BisnisUmkm extends Model
{
    use HasFactory;

    // Nama tabel yang disesuaikan dengan skema database 3NF Anda
    protected $table = 'bisnis_umkm';

    // Primary key kustom bertipe string sesuai rancangan database kelompok Anda
    protected $primaryKey = 'id_bisnis';

    // Menonaktifkan auto-increment karena primary key menggunakan string kustom
    public $incrementing = false;

    // Menentukan tipe data dari primary key
    protected $keyType = 'string';

    // Daftar field yang diizinkan untuk pengisian Mass Assignment di Controller
    protected $fillable = [
        'id_bisnis',
        'id_user',
        'nama_bisnis',
        'kategori_bisnis',
        'alamat_lengkap',
        'lama_beroperasi', // Menampung pilihan durasi operasional usaha (e.g., < 1 Tahun)
    ];

    /**
     * RELASI KEBALIKAN (Belongs To): 
     * Setiap satu entitas bisnis UMKM terikat dan dimiliki oleh satu User (Peminjam).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}