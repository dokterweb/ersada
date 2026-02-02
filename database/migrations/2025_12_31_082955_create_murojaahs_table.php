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
        Schema::create('murojaahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('ustadz_id')->nullable();
            $table->unsignedInteger('surat_id');
            $table->unsignedInteger('surat_no');
            $table->unsignedInteger('dariayat');
            $table->unsignedInteger('sampaiayat');
            $table->date('tgl_murojaah');
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
        Schema::dropIfExists('murojaahs');
    }
};
