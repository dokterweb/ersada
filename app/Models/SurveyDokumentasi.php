<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyDokumentasi extends Model
{
    use HasFactory;

    protected $fillable = ['survey_id','jaminan_pengajuan_id','kategori','posisi','file',];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function jaminan()
    {
        return $this->belongsTo(JaminanPengajuan::class,'jaminan_pengajuan_id');
    }
}
