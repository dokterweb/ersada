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
            $table->foreignId('pembiayaan_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_pelunasan')->unique();
            $table->date('tanggal_pelunasan');
            $table->bigInteger('sisa_pokok')->default(0);
            $table->bigInteger('sisa_bunga')->default(0);
            $table->bigInteger('denda')->default(0);
            $table->bigInteger('diskon')->default(0);
            $table->bigInteger('total_pelunasan')->default(0);
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
        Schema::dropIfExists('pelunasans');
    }
};
