<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengajuan extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['nomor_pengajuan', 'cabang_id', 'marketing_id', 'status', 'current_step','tanggal_pengajuan', 'nominal_pengajuan', 'tenor', 'tujuan_pinjaman','kategori_nasabah', 'catatan'];

   // cabang pengajuan
   public function cabang()
   {
       return $this->belongsTo(Cabang::class);
   }

   // marketing pemilik pengajuan
   public function marketing()
   {
       return $this->belongsTo(Karyawan::class, 'marketing_id');
   }

   // data nasabah utama
   public function nasabah()
   {
       return $this->hasOne(Nasabah::class);
   }

   // semua referensi (pasangan, penjamin, saudara)
   public function referensis()
   {
       return $this->hasMany(Referensi::class);
   }

   // dokumen upload
   public function dokumenPengajuans()
   {
       return $this->hasMany(Dokumen_pengajuan::class);
   }

   public function analisa()
    {
        return $this->hasOne(AnalisaPengajuan::class);
    }

    public function jaminans()
    {
        return $this->hasMany(JaminanPengajuan::class);
    }

    public function kapital()
    {
        return $this->hasOne(KapitalPengajuan::class);
    }

   public function pasangan()
    {
        return $this->hasOne(
            Referensi::class
        )->where('jenis','pasangan');
    }

    public function penjamin()
    {
        return $this->hasOne(
            Referensi::class
        )->where('jenis','penjamin');
    }

    public function saudaras()
    {
        return $this->hasMany(
            Referensi::class
        )->where('jenis','saudara');
    }
}
