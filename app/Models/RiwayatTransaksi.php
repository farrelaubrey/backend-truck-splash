<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatTransaksi extends Model
{
    use HasFactory;

    protected $table = 'riwayat_transaksi';
    protected $primaryKey = 'id_transaksi'; 
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_transaksi',
        'id_user',
        'id_pinjaman',
        'deskripsi',      // Contoh: "Angsuran ke-3 - Modal Warung Makan"
        'kategori',       // REPAYMENT, DISBURSEMENT, ADMIN FEE
        'jumlah',         // Nilai bisa minus (pengeluaran) atau plus (pencairan)
        'status',         // SUCCESS, PENDING, FAILED
        'tanggal_transaksi' // Tanggal beserta jam operasional transaksi
    ];

    // Relasi ke User pemilik transaksi
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}