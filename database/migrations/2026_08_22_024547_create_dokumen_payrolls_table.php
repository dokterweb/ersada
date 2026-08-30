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
        Schema::create('dokumen_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_dokumen');
            $table->string('nama_file');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_payrolls');
    }
};
