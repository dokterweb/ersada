<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranAngsuran extends Model
{
    protected $table = 'pembayaran_angsurans';
    protected $fillable = ['angsuran_id','nomor_pembayaran','tanggal_bayar','jumlah_bayar','denda',
        'diskon','total_dibayar','metode', 'keterangan', 'created_by',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'integer',
        'denda' => 'integer',
        'diskon' => 'integer',
        'total_dibayar' => 'integer',
    ];

    public function angsuran()
    {
        return $this->belongsTo(Angsuran::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
