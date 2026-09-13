<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JaminanPengajuan extends Model
{
    protected $fillable = ['pengajuan_id', 'jenis_jaminan', 'jenis_kendaraan', 'tahun_kendaraan', 'merk_kendaraan', 
    'plat_polisi', 'bpkb_status', 'pajak_stnk_status', 'status_pajak', 'bpkb_atas_nama', 'no_bpkb', 'no_rangka', 
    'no_mesin', 'nama_jaminan', 'skt_spgr_status', 'skt_spgr_dikeluarkan_oleh', 'no_sk_kerja', 'sertifikat_status', 'detail_jaminan', 'nilai_taksiran'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function dokumentasis()
    {
        return $this->hasMany(SurveyDokumentasi::class,'jaminan_pengajuan_id');
    }

    public function dokumenJaminans()
    {
        return $this->hasMany(DokumenJaminan::class,'jaminan_pengajuan_id');
    }
}
