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
        Schema::create('pekerjaan_nasabahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_pekerjaan')->nullable();
            $table->decimal('penghasilan',18,2)->nullable();
            $table->string('nama_usaha')->nullable();
            $table->string('jenis_usaha')->nullable();
            $table->integer('lama_usaha')->nullable();
            $table->integer('jumlah_pegawai')->nullable();
            $table->text('alamat_usaha')->nullable();
            $table->string('telpon_usaha')->nullable();
            $table->string('bangunan_usaha')->nullable();
            $table->string('status_tempat_usaha')->nullable();
            $table->string('aktivitas_usaha')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pekerjaan_nasabahs');
    }
};
