<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'nomor_telpon',
        'status_akun',
    ];

    /**
     * Kolom yang harus disembunyikan saat data diubah ke Array atau JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}