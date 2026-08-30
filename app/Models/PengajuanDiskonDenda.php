<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanDiskonDenda extends Model
{
      protected $fillable = ['angsuran_id','denda','diskon_denda','denda_setelah_diskon',
        'alasan','status','diajukan_oleh','disetujui_oleh','disetujui_at','catatan_approval', 'pembayaran_id',
        'digunakan_at',];

    protected $casts = [
        'disetujui_at' => 'datetime',
        'digunakan_at' => 'datetime',
    ];


    public function angsuran()
    {
        return $this->belongsTo(Angsuran::class);
    }


    public function diajukanOleh()
    {
        return $this->belongsTo(User::class,'diajukan_oleh');
    }


    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class,'disetujui_oleh');
    }

    public function pembayaran()
    {
        return $this->belongsTo(
            PembayaranAngsuran::class,
            'pembayaran_id'
        );
    }
}
