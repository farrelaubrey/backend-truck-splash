<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BisnisUmkm extends Model
{
    
    protected $table = 'bisnis_umkm'; 

    protected $fillable = [
        'user_id',
        'nama_bisnis',
        'kategori_bisnis',
        'usia_bisnis',
        'alamat_lengkap',
    ];
}