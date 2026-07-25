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
        Schema::create('pembiayaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_pembiayaan')->unique();
            $table->decimal('plafond', 18, 2);
            $table->unsignedTinyInteger('tenor');
            $table->enum('jenis_tenor', ['pendek', 'panjang']);
            $table->decimal('persen_bunga', 5, 2);
            $table->decimal('persen_administrasi', 5, 2)->default(3.00);
            $table->decimal('biaya_administrasi', 18, 2)->default(0);
            $table->decimal('materai', 18, 2)->default(0);
            $table->decimal('biaya_survei', 18, 2)->default(0);
            $table->decimal('dana_diterima', 18, 2)->default(0);
            $table->date('tanggal_akad')->nullable();
            $table->date('tanggal_pencairan')->nullable();
            $table->date('tanggal_jatuh_tempo_pertama')->nullable();
            $table->enum('status', ['draft','review','siap_generate_jadwal','akad','dicairkan'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembiayaans');
    }
};
