<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('angsurans', function (Blueprint $table) {

            $table->foreignId('pelunasan_id')
                ->nullable()
                ->after('pembiayaan_id')
                ->constrained('pelunasans')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('angsurans', function (Blueprint $table) {

            $table->dropForeign(['pelunasan_id']);
            $table->dropColumn('pelunasan_id');

        });
    }
};