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
        Schema::create('hariliburs', function (Blueprint $table) {
            $table->id();
            // Bisa 1 hari (tanggal_mulai = tanggal_selesai)
            // atau range libur
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // Contoh: "Libur Idul Fitri", "Class Meeting", dll.
            $table->string('nama_libur');

            // nasional: libur nasional
            // sekolah : kebijakan sekolah sendiri
            // mingguan: pola libur tiap minggu (misal Jumat/Ahad)
            $table->enum('tipe', ['nasional', 'sekolah', 'mingguan'])->default('sekolah');

            // siapa yang kena libur
            $table->enum('berlaku_untuk', ['semua', 'siswa', 'ustadz'])->default('semua');

            // keterangan tambahan (opsional)
            $table->string('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hariliburs');
    }
};
