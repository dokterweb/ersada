<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KapitalPengajuan extends Model
{
    protected $fillable = ['pengajuan_id','omzet_harian','laba_harian','gaji_debitur','pendapatan_pasangan','biaya_rumah_tangga','biaya_motor',
        'biaya_koperasi','angsuran_lain','biaya_kontrak_rumah','biaya_tempat_usaha','total_pengeluaran','sisa_pendapatan','created_by'];


    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
