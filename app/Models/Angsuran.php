<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory;
    protected $fillable = ['pembiayaan_id','angsuran_ke','tanggal_jatuh_tempo','pokok_angsuran','bunga_angsuran','total_angsuran',
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
}
