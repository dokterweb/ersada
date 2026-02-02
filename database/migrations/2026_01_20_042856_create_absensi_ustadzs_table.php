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
        Schema::create('absensi_ustadzs', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_absen');
            $table->enum('status', ['hadir', 'ghoib', 'izin', 'tugas', 'sakit', 'pulang']); // Status absensi
            $table->foreignId('ustadz_id')->constrained()->onDelete('cascade');
            $table->string('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_ustadzs');
    }
};
