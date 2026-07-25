<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JaminanPengajuan extends Model
{
    protected $fillable = ['pengajuan_id','jenis_jaminan','nama_jaminan','detail_jaminan','nilai_taksiran'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function dokumentasis()
    {
        return $this->hasMany(SurveyDokumentasi::class,'jaminan_pengajuan_id');
    }
}
