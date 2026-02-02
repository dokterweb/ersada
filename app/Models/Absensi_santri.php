<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi_santri extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable=['tgl_absen', 'status', 'santri_id', 'keterangan'];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id'); 
    }

}
