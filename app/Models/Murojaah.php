<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Murojaah extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable=['santri_id', 'ustadz_id', 'surat_id', 'surat_no', 'dariayat', 'sampaiayat', 'tgl_murojaah', 'keterangan'];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id');
    }

    public function surat()
    {
        return $this->belongsTo(Madina::class, 'surat_id');
    }
    
    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }
}
