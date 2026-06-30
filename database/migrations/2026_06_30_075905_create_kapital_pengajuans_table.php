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
        Schema::create('kapital_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained()->cascadeOnDelete();
            // PENDAPATAN
            $table->bigInteger('omzet_harian')->nullable();
            $table->bigInteger('laba_harian')->nullable();
            $table->bigInteger('pendapatan_lain')->nullable();
            $table->bigInteger('pendapatan_pasangan')->nullable();
            // PENGELUARAN
            $table->bigInteger('biaya_rumah_tangga')->nullable();
            $table->bigInteger('biaya_motor')->nullable();
            $table->bigInteger('biaya_koperasi')->nullable();
            $table->bigInteger('angsuran_lain')->nullable();
            $table->bigInteger('biaya_kontrak_rumah')->nullable();
            $table->bigInteger('biaya_tempat_usaha')->nullable();
            // HASIL ANALISA
            $table->bigInteger('total_pengeluaran')->nullable();
            $table->bigInteger('sisa_pendapatan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kapital_pengajuans');
    }
};
