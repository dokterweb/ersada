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
        Schema::create('jaminan_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_jaminan');
            $table->string('jenis_kendaraan')->nullable();
            $table->unsignedsmallInteger('tahun_kendaraan')->nullable();
            $table->string('merk_kendaraan')->nullable();
            $table->string('plat_polisi')->nullable();
            $table->string('bpkb_status')->nullable();
            $table->string('pajak_stnk_status')->nullable();
            $table->string('status_pajak')->nullable();
            $table->string('bpkb_atas_nama')->nullable();
            $table->string('no_bpkb')->nullable();
            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('nama_jaminan');
            $table->string('skt_spgr_status');
            $table->string('skt_spgr_dikeluarkan_oleh');
            $table->string('sertifikat_status');
            $table->string('no_sk_kerja')->nullable();
            $table->text('detail_jaminan')->nullable();
            $table->bigInteger('nilai_taksiran')->nullable();
            $table->string('file_jaminan')->nullable()->after('nilai_taksiran');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_jaminan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jaminan_pengajuans');
    }
};
