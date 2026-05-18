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
    $table->string('id_limit', 10)->primary();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->bigInteger('total_limit');
    $table->bigInteger('limit_terpakai');
    $table->timestamps();
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
