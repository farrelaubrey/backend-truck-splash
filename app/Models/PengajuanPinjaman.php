<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Perbaikan dari 'use Factory'
use Illuminate\Database\Eloquent\Model;

class PengajuanPinjaman extends Model
{
    use HasFactory; // Menggunakan HasFactory yang benar

    protected $table = 'pengajuan_pinjaman';
    protected $primaryKey = 'id_pinjaman'; // Primary key string dari rancangan 3NF 
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pinjaman',
        'id_user',
        'jumlah_pinjaman',
        'tenor',
        'status_pengajuan',
        
        // 1. Identitas & Eksistensi (Langkah 2 - Form Legalitas) 
        'bukti_ktp_diri',
        'foto_tempat_usaha',
        'surat_izin_usaha', // NIB/SKU 
        
        // 2. Dokumen Keuangan (Langkah 2) 
        'laporan_arus_kas',
        'pendapatan_bulanan',
        'rekening_koran',
        
        // 3. Aset & Jaminan (Langkah 2 - Opsional) 
        'foto_aset',
        'stnk_bpkb',
        
        'id_admin_verifikator' // Diambil dari rancangan relasi tabel database kalian 
    ];

    /**
     * RELASI KEBALIKAN: Pinjaman ini diajukan oleh satu User 
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * RELASI: Satu pengajuan pinjaman memiliki banyak tagihan cicilan (1 to Many) 
     * Wajib ditambahkan agar data progress bar pelunasan (75% & 10%) 
     * dan info cicilan (3/4 Bln) bisa terbaca di halaman "Pinjaman Saya".
     */
    public function tagihan()
    {
        return $this->hasMany(PembayaranTagihan::class, 'id_pinjaman', 'id_pinjaman');
    }
}