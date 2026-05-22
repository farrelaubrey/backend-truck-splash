<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user'; // [cite: 121]
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
    'id_user',
    'nama_lengkap',
    'email',
    'nomor_telpon', // Atau nomor_whatsapp
    'password',
    'foto_profil',  // Path penyimpanan berkas foto (Maks 2MB)
    'status_akun',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: Satu User memiliki Satu Bisnis UMKM
    public function bisnis()
    {
        return $this->hasOne(BisnisUmkm::class, 'id_user', 'id_user');
    }

    // Relasi: Satu User memiliki Banyak Pengajuan Pinjaman
    public function pengajuanPinjaman()
    {
        return $this->hasMany(PengajuanPinjaman::class, 'id_user', 'id_user');
    }

    // Relasi: Satu User memiliki Satu Limit Pinjaman
    public function limitPinjaman()
    {
        return $this->hasOne(LimitPinjaman::class, 'id_user', 'id_user');
    }
}