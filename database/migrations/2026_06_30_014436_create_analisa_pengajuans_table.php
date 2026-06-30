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
        Schema::create('analisa_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained()->cascadeOnDelete();
            // CHECKLIST PERSETUJUAN
            // Harga kredit → setuju / tidak setuju
            $table->boolean('harga_kredit')->nullable();
            // Kewajiban angsuran → sudah dijelaskan / belum
            $table->boolean('kewajiban_angsuran')->nullable();
            // STATUS PEMOHON  pemilik / karyawan / pengelola
            $table->string('status_pemohon')->nullable();
            // STATUS TU / RM milik / kontrak
            $table->string('status_tempat_tinggal')->nullable();
            $table->boolean('data_pemohon_lengkap')->nullable();
            $table->boolean('ktp_pemohon_valid')->nullable();
            $table->boolean('ktp_pasangan_valid')->nullable();
            $table->boolean('kk_valid')->nullable();
            $table->boolean('perbaikan_plafon')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisa_pengajuans');
    }
};
