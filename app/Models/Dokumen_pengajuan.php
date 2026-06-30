<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen_pengajuan extends Model
{
    protected $fillable = ['pengajuan_id','jenis_dokumen','nama_file','file_path','file_size','status','catatan',
        'uploaded_by',
    ];

    // Relasi ke pengajuan
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    // User uploader
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getJenisLabelAttribute()
    {
        return ucwords(str_replace('_',' ',$this->jenis_dokumen));
    }

    
}
