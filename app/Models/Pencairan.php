<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pencairan extends Model
{
    protected $fillable = ['akad_id','nomor_pencairan','tanggal_pencairan', 'tgl_telat_bayar', 'jumlah_dicairkan','metode','bank',
        'no_rekening','atas_nama','bukti_pencairan','foto_akad1','foto_akad2','foto_akad3','video','keterangan','created_by',];

    protected $casts = [
        'tanggal_pencairan' => 'date',
        'jumlah_dicairkan' => 'integer',
    ];


    public function akad()
    {
        return $this->belongsTo(Akad::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
