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
        Schema::create('akads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembiayaan_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_akad')->unique();
            $table->date('tanggal_akad');
            $table->string('tempat_akad')->nullable();
            $table->string('nomor_perjanjian')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status',['draft','signed',])->default('draft');
            $table->string('file_word')->nullable();
            $table->string('file_pdf')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akads');
    }
};
