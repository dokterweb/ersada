<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi_ustadz extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable=['tgl_absen', 'status', 'ustadz_id', 'keterangan'];

   // Relasi dengan model Ustadz
   public function ustadz()
   {
       return $this->belongsTo(Ustadz::class, 'ustadz_id');
   }
    
}
