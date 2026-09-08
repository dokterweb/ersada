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
    Schema::create('pelunasans', function (Blueprint $table) {

        $table->id();

        $table->foreignId('pembiayaan_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('nomor_pelunasan')
            ->unique();

        $table->date('tanggal_pelunasan');


        /*
        |--------------------------------------------------------------------------
        | JENIS PELUNASAN
        |--------------------------------------------------------------------------
        */

        $table->enum('jenis_pelunasan', [
            'normal',
            'dengan_diskon'
        ])
        ->default('normal');


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        $table->enum('status', [
            'diajukan',
            'menunggu_persetujuan',
            'disetujui',
            'ditolak',
            'siap_dibayar',
            'dibayar',
            'dibatalkan',
        ])
        ->default('diajukan');


        /*
        |--------------------------------------------------------------------------
        | PERHITUNGAN
        |--------------------------------------------------------------------------
        */

        $table->bigInteger('sisa_pokok')
            ->default(0);

        $table->bigInteger('sisa_bunga')
            ->default(0);

        $table->bigInteger('denda')
            ->default(0);

        $table->bigInteger('total_sebelum_diskon')
            ->default(0);


        /*
        |--------------------------------------------------------------------------
        | DISKON
        |--------------------------------------------------------------------------
        */

        // Diskon yang diajukan
        $table->bigInteger('diskon')
            ->default(0);

        // Diskon yang disetujui pimpinan
        $table->bigInteger('diskon_disetujui')
            ->nullable();

        // Total akhir yang harus dibayar
        $table->bigInteger('total_pelunasan')
            ->default(0);


        /*
        |--------------------------------------------------------------------------
        | KETERANGAN PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $table->text('alasan_diskon')
            ->nullable();

        $table->text('keterangan')
            ->nullable();


        /*
        |--------------------------------------------------------------------------
        | USER PEMBUAT
        |--------------------------------------------------------------------------
        */

        $table->foreignId('created_by')
            ->constrained('users');


        /*
        |--------------------------------------------------------------------------
        | APPROVAL PIMPINAN
        |--------------------------------------------------------------------------
        */

        $table->foreignId('approved_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->timestamp('approved_at')
            ->nullable();

        $table->text('catatan_approval')
            ->nullable();


        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $table->foreignId('paid_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->timestamp('paid_at')
            ->nullable();


        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelunasans');
    }
};
