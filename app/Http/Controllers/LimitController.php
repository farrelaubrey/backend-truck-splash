<?php

namespace App\Http\Controllers;

use App\Models\LimitPinjaman;
use Illuminate\Support\Facades\Auth;

class LimitController extends Controller
{
    // Skenario: Monitoring Dashboard Utama terkait sisa limit
    public function checkLimit()
    {
        $limit = LimitPinjaman::where('id_user', Auth::id())->first();

        $limit_tersedia = $limit->total_limit - $limit->limit_terpakai;

        // Skenario Alternatif jika limit mendekati nol atau habis terpakai maksimal
        if ($limit_tersedia <= 0) {
            return view('peminjam.dashboard', [
                'limit_color' => 'dark-green', // Indikator warna hijau tua/semakin gelap pada bar limit
                'limit_message' => 'Limit tidak tersedia untuk pengajuan baru',
                'limit' => $limit
            ]);
        }

        return view('peminjam.dashboard', [
            'limit_color' => 'normal-green',
            'limit_message' => 'Limit tersedia',
            'limit' => $limit
        ]);
    }
}