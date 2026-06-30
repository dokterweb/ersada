<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerjaan_referensi extends Model
{
    use HasFactory;

    protected $fillable = ['referensi_id','jenis_pekerjaan','penghasilan','nama_usaha','jenis_usaha','lama_usaha',
    'jumlah_pegawai','alamat_usaha','telpon_usaha'];

    public function referensi()
    {
        return $this->belongsTo(Referensi::class);
    }
}
