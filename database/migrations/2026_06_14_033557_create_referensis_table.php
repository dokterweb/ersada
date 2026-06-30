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
        Schema::create('referensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained()->cascadeOnDelete();
            // pasangan, penjamin,  saudara1, saudara2 
            $table->string('jenis');
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            // hanya dipakai penjamin/saudara
            $table->string('hubungan')->nullable();
            // penjamin/saudara
            $table->text('alamat')->nullable();
            $table->integer('urutan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referensis');
    }
};
