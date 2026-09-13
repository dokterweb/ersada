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
        Schema::create('pencairans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akad_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_pencairan')->unique();
            $table->date('tanggal_pencairan');
            $table->unsignedTinyInteger('tgl_telat_bayar')->default(10);
            $table->bigInteger('jumlah_dicairkan');
            $table->string('metode')->default('tunai');
            // tunai
            // transfer
            $table->string('bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('atas_nama')->nullable();
            $table->string('bukti_pencairan')->nullable();
            $table->string('foto_akad1')->nullable();
            $table->string('foto_akad2')->nullable();
            $table->string('foto_akad3')->nullable();
            $table->string('video')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairans');
    }
};
