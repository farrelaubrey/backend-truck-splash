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
        Schema::create('limit_pinjaman', function (Blueprint $table) {
            $table->string('id_limit', 10)->primary(); // Sesuai laporan [cite: 76, 111, 121]
            $table->string('id_user', 10)->unique(); // Wajib id_user (bukan user_id) agar sinkron dengan tabel users [cite: 77, 112, 122]
            $table->bigInteger('total_limit'); // Sesuai laporan [cite: 77, 113, 123]
            $table->bigInteger('limit_terpakai'); // Sesuai laporan [cite: 77, 114, 123]
            $table->timestamps();

            // Relasi Foreign Key yang benar menunjuk ke id_user milik tabel users
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('limit_pinjaman');
    }
};