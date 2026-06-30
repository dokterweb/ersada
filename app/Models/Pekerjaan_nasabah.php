<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerjaan_nasabah extends Model
{
    use HasFactory;

    protected $fillable = ['nasabah_id','jenis_pekerjaan','penghasilan','nama_usaha','jenis_usaha','lama_usaha',
    'jumlah_pegawai','alamat_usaha','telpon_usaha','bangunan_usaha','status_tempat_usaha','aktivitas_usaha',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}
