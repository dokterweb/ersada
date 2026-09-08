<?php

namespace App\Models;

use App\Models\Pelunasan;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Models\PengajuanDiskonDenda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory;
    protected $fillable = ['pembiayaan_id','pelunasan_id','angsuran_ke','tanggal_jatuh_tempo','pokok_angsuran','bunga_angsuran','total_angsuran',
                            'sisa_pokok','tanggal_bayar','jumlah_bayar','denda','total_terbayar','sisa_tagihan','status',];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
    ];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function pembayaranAngsurans()
    {
        return $this->hasMany(PembayaranAngsuran::class);
    }

    public function pengajuanDiskonDendas()
    {
        return $this->hasMany(PengajuanDiskonDenda::class);
    }

    public function pelunasan()
    {
        return $this->belongsTo(Pelunasan::class);
    }

    public function getMetodePelunasanAttribute()
    {
        return $this->pelunasan_id? 'Pelunasan': 'Pembayaran Angsuran';
    }
}
