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
        Schema::create('pengajuan_pinjaman', function (Blueprint $table) {
            $table->string('id_pinjaman', 10)->primary(); // Sesuai laporan [cite: 98, 131]
            $table->string('id_user', 10); // Foreign Key ke users [cite: 99, 131]
            $table->bigInteger('jumlah_pinjaman'); // Sesuai laporan [cite: 100, 131]
            $table->integer('tenor'); // Sesuai laporan [cite: 101, 131]
            $table->string('bukti_ktp', 255); // Sesuai laporan [cite: 131]
            $table->string('bukti_foto_usaha', 255); // Sesuai laporan [cite: 131]
            $table->string('status_pengajuan', 50); // Sesuai laporan [cite: 102, 131]
            
            // Pastikan tipe data dan panjangnya (10) sama persis dengan id_admin di tabel admin
            $table->string('id_admin_verifikator', 10)->nullable(); // Sesuai laporan [cite: 131]
            $table->timestamps();

            // Relasi Foreign Key
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_admin_verifikator')->references('id_admin')->on('admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pinjaman');
    }
};