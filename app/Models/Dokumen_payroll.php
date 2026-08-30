<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen_payroll extends Model
{
      protected $fillable = ['pengajuan_id','jenis_dokumen','nama_file','file_path','file_size','mime_type','uploaded_by',];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }


    public function uploader()
    {
        return $this->belongsTo(User::class,'uploaded_by');
    }
}
