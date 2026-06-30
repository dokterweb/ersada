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
        Schema::create('dokumen_pengajuans', function (Blueprint $table) {
            $table->id();
           // relasi ke pengajuan
            $table->foreignId('pengajuan_id')->constrained()->cascadeOnDelete();

            /*
            jenis dokumen, contoh:
            ktp_pemohon, ktp_pasangan, kk,slip_gaji,npwp,bpkb,stnk,surat_usaha,foto_jaminan
            */
            $table->string('jenis_dokumen');

            // nama file asli
            $table->string('nama_file');

            // path storage
            $table->string('file_path');

            // ukuran file KB
            $table->integer('file_size')->nullable();

            /* 
            status validasi admin
            pending , valid, invalid, revisi
            */
            $table->string('status')->default('pending');

            // catatan admin jika dokumen salah
            $table->text('catatan')->nullable();

            // siapa yang upload
            $table->foreignId('uploaded_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuans');
    }
};
