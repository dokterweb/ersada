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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->unsignedInteger('cabang_id');
            $table->foreign('cabang_id')->references('id')->on('cabangs');
            // Marketing pemilik pengajuan
            $table->foreignId('marketing_id')->constrained('karyawans');
            // Status sederhana (atau bisa diganti status_id)
            $table->string('status')->default('draft');
            $table->integer('current_step')->default(1);
            $table->date('tanggal_pengajuan');
            $table->decimal('nominal_pengajuan', 18, 2)->nullable();
            $table->integer('tenor')->nullable();
            $table->text('tujuan_pinjaman')->nullable();
            $table->string('status_customer')->nullable();
            $table->decimal('angsuran',18,2)->nullable();
            $table->text('catatan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
