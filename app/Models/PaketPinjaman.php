<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketPinjaman extends Model
{
    use HasFactory;

    protected $table = 'paket_pinjaman';
    protected $primaryKey = 'id_paket';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_paket',
        'nama_paket',
        'sektor_kategori', // Kuliner, Warung, Jasa
        'deskripsi',
        'tenor_min',
        'tenor_max',
        'satuan_tenor', // Hari, Bulan
        'bunga_per_bulan',
    ];
}