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
        Schema::create('pembayaran_angsurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('angsuran_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_pembayaran')->unique();
            $table->date('tanggal_bayar');
            $table->bigInteger('jumlah_bayar');
            $table->bigInteger('denda')->default(0);
            $table->unsignedBigInteger('admin_keterlambatan')->default(0);
            $table->bigInteger('total_dibayar');
            $table->enum('metode',['tunai','transfer'])->default('tunai');
            $table->bigInteger('diskon_denda')->default(0);
            $table->enum('status_diskon', ['tidak_ada','menunggu','disetujui','ditolak',])->default('tidak_ada');
            $table->foreignId('diskon_disetujui_oleh')->nullable()->constrained('users');
            $table->timestamp('diskon_disetujui_at')->nullable();
            $table->text('alasan_diskon')->nullable();
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
        Schema::dropIfExists('pembayaran_angsurans');
    }
};
