<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referensi extends Model
{
    use HasFactory;

    protected $fillable = ['pengajuan_id','jenis','nama','tempat_lahir','tgl_lahir','hubungan','no_hp','alamat','urutan'];

    const PASANGAN = 'pasangan';
    const PENJAMIN = 'penjamin';
    const SAUDARA = 'saudara';

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function pekerjaan()
    {
        return $this->hasOne(Pekerjaan_referensi::class,'referensi_id');
    }
}
