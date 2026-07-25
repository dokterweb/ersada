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
        Schema::create('survey_berkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->boolean('ktp_debitur')->default(false);
            $table->boolean('kk_debitur')->default(false);
            $table->boolean('ktp_pasangan')->default(false);
            $table->boolean('kk_pasangan')->default(false);
            $table->enum('status_peminjam',['baru','lama'])->nullable();
            $table->unsignedInteger('plafond_pinjaman_lama')->nullable();
            $table->boolean('ktp_penjamin')->default(false);
            $table->boolean('kk_penjamin')->default(false);
            $table->string('nama_pasangan_penjamin')->nullable();
            $table->boolean('ktp_pasangan_penjamin')->default(false);
            $table->boolean('kk_pasangan_penjamin')->default(false);
            $table->boolean('slip_gaji')->default(false);
            $table->string('no_id_karyawan')->nullable();
            $table->unsignedSmallInteger('lama_bekerja')->nullable();
            $table->string('no_telp_karyawan')->nullable();
            $table->boolean('bpjs')->default(false);
            $table->string('no_bpjs')->nullable();
            $table->boolean('buku_tabungan')->default(false);
            $table->string('nama_bank')->nullable();
            $table->boolean('kartu_atm')->default(false);
            $table->string('pin_atm')->nullable();
            $table->text('catatan_kekurangan')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_berkas');
    }
};
