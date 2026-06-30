<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisaPengajuan extends Model
{
    protected $fillable = ['pengajuan_id','harga_kredit','kewajiban_angsuran','status_pemohon','status_tempat_tinggal',
    'data_pemohon_lengkap','ktp_pemohon_valid','ktp_pasangan_valid','kk_valid','perbaikan_plafon','created_by'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
