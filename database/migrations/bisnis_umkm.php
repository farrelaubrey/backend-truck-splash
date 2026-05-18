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
            $table->id(); // id_bisnis
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_bisnis');
            $table->string('kategori_bisnis');
            $table->integer('usia_bisnis');
            $table->text('alamat_bisnis');
            $table->timestamps();
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