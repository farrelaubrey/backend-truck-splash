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
        Schema::create('bisnis_umkm', function (Blueprint $table) {
            $table->string('id_bisnis', 10)->primary();
            $table->string('id_user', 10); // Foreign Key ke tabel users
            $table->string('nama_bisnis', 100);
            $table->string('kategori_bisnis', 50);
            $table->integer('usia_bisnis');
            $table->text('alamat_lengkap');
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bisnis_umkm');
    }
};