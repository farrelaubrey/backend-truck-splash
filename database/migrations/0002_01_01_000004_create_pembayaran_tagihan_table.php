<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayaran_tagihan', function (Blueprint $table) {
            $table->string('id_tagihan', 10)->primary(); // Sesuai laporan [cite: 75, 105, 127]
            $table->string('id_pinjaman', 10); // Foreign Key (Wajib String 10) [cite: 75, 106, 128]
            $table->bigInteger('jumlah_tagihan'); // Sesuai laporan [cite: 75, 107, 128]
            $table->date('jatuh_tempo'); // Sesuai laporan [cite: 75, 108, 129]
            $table->string('status_pembayaran', 50); // Sesuai laporan [cite: 75, 109, 130]
            $table->timestamps();

            // Memperbaiki referensi ke id_pinjaman (bukan id) 
            $table->foreign('id_pinjaman')->references('id_pinjaman')->on('pengajuan_pinjaman')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_tagihan');
    }
};