<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nasabah extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['pengajuan_id', 'nama', 'nik', 'tempat_lahir', 'tgl_lahir', 'no_hp', 'alamat', 'status_perkawinan', 'jumlah_tanggungan', 'status_rumah', 'lama_menetap_tahun', 'lama_menetap_bulan'];

    // Nasabah.php
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

     // pekerjaan nasabah (wajib)
     public function pekerjaan()
     {
         return $this->hasOne(Pekerjaan_nasabah::class);
     }
}
