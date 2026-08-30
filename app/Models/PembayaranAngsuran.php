<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranAngsuran extends Model
{
    protected $table = 'pembayaran_angsurans';
    protected $fillable = ['angsuran_id', 'nomor_pembayaran', 'tanggal_bayar', 'jumlah_bayar', 'denda', 'diskon_denda', 'admin_keterlambatan', 
    'total_dibayar', 'status_diskon', 'diskon_disetujui_oleh', 'diskon_disetujui_at', 'alasan_diskon', 'metode', 'keterangan', 'created_by'];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'diskon_disetujui_at' => 'datetime',
    ];

    public function angsuran()
    {
        return $this->belongsTo(Angsuran::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

       public function diskonDisetujuiOleh()
    {
        return $this->belongsTo(User::class,'diskon_disetujui_oleh');
    }
}
