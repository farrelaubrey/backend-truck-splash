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
        $table->string('id_tagihan', 10)->primary();
        $table->foreignId('id_pinjaman')->constrained('pengajuan_pinjaman');
        $table->bigInteger('jumlah_tagihan');
        $table->date('jatuh_tempo');
        $table->string('status_pembayaran', 50);
        $table->timestamps();
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
