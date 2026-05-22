<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin'; 
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_admin',
        'nama_staff',
        'email',
        'password',
        'role', // SUPERADMIN, VERIFIKATOR, DATA PENGGUNA
        'status', // Aktif, Offline, Ditangguhkan
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi: Satu Admin dapat memverifikasi banyak pengajuan mitra
     */
    public function verifikasiPengajuan()
    {
        return $this->hasMany(PengajuanPinjaman::class, 'id_admin_verifikator', 'id_admin');
    }
}