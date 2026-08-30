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
        Schema::create('angsurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembiayaan_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('angsuran_ke');
            $table->date('tanggal_jatuh_tempo');
            $table->bigInteger('pokok_angsuran')->default(0);
            $table->bigInteger('bunga_angsuran')->default(0);
            $table->bigInteger('total_angsuran')->default(0);
            $table->bigInteger('sisa_pokok')->default(0);
            $table->date('tanggal_bayar')->nullable();
            $table->bigInteger('jumlah_bayar')->default(0);
            $table->bigInteger('denda')->default(0);
            $table->unsignedBigInteger('admin_keterlambatan')->default(0);
            $table->bigInteger('total_terbayar')->default(0);
            $table->bigInteger('sisa_tagihan')->default(0);
            $table->enum('status',['belum_jatuh_tempo','jatuh_tempo','dibayar'])->default('belum_jatuh_tempo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsurans');
    }
};
