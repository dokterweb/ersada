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
        Schema::create('pengajuan_diskon_dendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('angsuran_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('denda');
            $table->unsignedBigInteger('diskon_denda');
            $table->unsignedBigInteger('denda_setelah_diskon');
            $table->text('alasan');
            $table->enum('status', ['pending','disetujui','ditolak',])->default('pending');
            $table->foreignId('diajukan_oleh')->constrained('users');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users');
            $table->timestamp('disetujui_at')->nullable();
            $table->foreignId('pembayaran_id')->nullable()->constrained('pembayaran_angsurans')->nullOnDelete();
            $table->timestamp('digunakan_at')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_diskon_dendas');
    }
};
