<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Harilibur extends Model
{
    use HasFactory;

    protected $table = 'hariliburs';

    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'nama_libur',
        'tipe',           // nasional / sekolah / mingguan
        'berlaku_untuk',  // semua / siswa / ustadz
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Scope: filter berdasarkan siapa yang terkena libur
     * contoh: Harilibur::berlakuUntuk('siswa')->get();
     */
    public function scopeBerlakuUntuk($query, string $jenis)
    {
        // 'semua' SELALU ikut, plus jenis spesifik
        return $query->whereIn('berlaku_untuk', ['semua', $jenis]);
    }

    /**
     * Cek apakah suatu tanggal adalah hari libur untuk jenis tertentu
     * $jenis: 'siswa' / 'ustadz'
     */
    public static function isLibur(string|\Carbon\Carbon $tanggal, string $jenis): bool
    {
        return static::berlakuUntuk($jenis)
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->exists();
    }

    /**
     * Kalau kamu perlu info detail liburnya (bukan cuma true/false)
     */
    public static function findLibur(string|\Carbon\Carbon $tanggal, string $jenis): ?self
    {
        return static::berlakuUntuk($jenis)
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->first();
    }
}
