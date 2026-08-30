<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenJaminan extends Model
{
     protected $fillable = ['jaminan_pengajuan_id','jenis_dokumen','nama_file','file_path','file_size','mime_type','uploaded_by',];

    public function jaminan()
    {
        return $this->belongsTo(JaminanPengajuan::class,'jaminan_pengajuan_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class,'uploaded_by');
    }
}
