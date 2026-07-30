<?php

namespace App\Services;

use App\Models\Akad;
use App\Services\AuditTrailService;
use Carbon\Carbon;

class AkadService
{
    public function __construct(
        AuditTrailService $auditTrail
    ) {
        $this->auditTrail = $auditTrail;
    }

    public function templateData(Akad $akad): array
    {
        $akad->load([
            'pembiayaan',
            'pembiayaan.angsurans',
            'pembiayaan.pengajuan',
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.nasabah.pekerjaanNasabah',
            'pembiayaan.pengajuan.marketing.user',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.referensis',
            'pembiayaan.pengajuan.referensis.pekerjaan',
            'pembiayaan.pengajuan.jaminanPengajuans',
            'pembiayaan.pengajuan.survey',
        ]);
    
        $pembiayaan = $akad->pembiayaan;
        $pengajuan  = $pembiayaan->pengajuan;
        $nasabah    = $pengajuan->nasabah;
        $pekerjaan = optional($nasabah)->pekerjaanNasabah;
        
        $jaminans   = $pengajuan->jaminanPengajuans;
    
        //REFERENSI
        $pasangan = optional($pengajuan->referensis->where('jenis', 'pasangan')->first());
        $penjamin = optional($pengajuan->referensis->where('jenis', 'penjamin')->first());
        // $pekerjaanpasangan  = optional($pasangan)->pekerjaan;

       /*  $pasangan = $pengajuan->pasangan;
        $penjamin = $pengajuan->penjamin; */
        $pekerjaanpasangan = optional($pasangan)->pekerjaan;
        $pekerjaanpenjamin = optional($penjamin)->pekerjaan;

        // ANGSURAN
        $angsuranPertama = $pembiayaan->angsurans->sortBy('angsuran_ke')->first();
        $angsuranTerakhir = $pembiayaan->angsurans->sortByDesc('angsuran_ke')->first();
        $batasDendaPertama = Carbon::parse($angsuranPertama->tanggal_jatuh_tempo)->addDay();
        $mulaiDendaPertama = Carbon::parse($angsuranPertama->tanggal_jatuh_tempo)->addDays(2);

        // JAMINAN
        $jaminan1 = $jaminans->get(0);
        $jaminan2 = $jaminans->get(1);
        $jaminan3 = $jaminans->get(2);

        $this->auditTrail->log(
            $pembiayaan,
            'Akad',
            'Generate Akad',
            'Nomor Akad : '.$akad->nomor_akad
        );

        return [
            // COMPANY
            'nama_perusahaan'       => 'PT. ERSADA MAKMUR JAYA',
            'nama_cabang'           => optional($pengajuan->cabang)->nama_cabang,
            'alamat_cabang'         => optional($pengajuan->cabang)->alamat,
            'telepon_cabang'        => optional($pengajuan->cabang)->telepon,
            'email_cabang'          => optional($pengajuan->cabang)->email,
            'hari_akad'             => $akad->tanggal_akad ? Carbon::parse($akad->tanggal_akad)->translatedFormat('l'): null,
            'tgl_akad'              => $akad->tanggal_akad ? Carbon::parse($akad->tanggal_akad)->format('d'): null,
            'bulan_akad'            => $akad->tanggal_akad ? Carbon::parse($akad->tanggal_akad)->translatedFormat('F'): null,
            'tahun_akad'            => $akad->tanggal_akad ? Carbon::parse($akad->tanggal_akad)->format('Y'): null,
    
            // AKAD
            'nomor_akad'           => $akad->nomor_akad,
            'nomor_perjanjian'     => $akad->nomor_perjanjian,
            'tanggal_akad'         => optional($akad->tanggal_akad)->format('d-m-Y'),
            'tempat_akad'          => $akad->tempat_akad,

            // PENGAJUAN
            'nomor_pengajuan'      => $pengajuan->nomor_pengajuan,
            'tanggal_pengajuan'    => optional($pengajuan->tanggal_pengajuan)->format('d-m-Y'),
            'tujuan_pinjaman'      => $pengajuan->tujuan_pinjaman,
    
            //NASABAH
            'nama'                 => $nasabah->nama,
            'nik'                  => $nasabah->nik,
            'tempat_lahir'         => $nasabah->tempat_lahir,
            'tanggal_lahir'        => optional($nasabah->tanggal_lahir)->format('d-m-Y'),
            'umur'                 => $nasabah->tanggal_lahir? Carbon::parse($nasabah->tanggal_lahir)->age: null,
            'alamat'               => $nasabah->alamat,
            'desa'                 => $nasabah->desa,
            'kecamatan'            => $nasabah->kecamatan,
            'kabupaten'            => $nasabah->kabupaten,
            'provinsi'             => $nasabah->provinsi,
            'kode_pos'             => $nasabah->kode_pos,
            'no_hp'                => $nasabah->no_hp,
            'agama'                => $nasabah->agama,
            'status_perkawinan'    => $nasabah->status_perkawinan,
    
            // PEKERJAAN
            'jenis_pekerjaan'       => $pekerjaan->jenis_pekerjaan,
            'nama_usaha'            => $pekerjaan->nama_usaha,
            'alamat_susaha'         => $pekerjaan->alamat_susaha,
            'lama_usaha'            => $pekerjaan->lama_usaha,
            'penghasilan'           => $pekerjaan->penghasilan,
    
            // PASANGAN
            'nama_pasangan'        => $pasangan->nama,
            'nik_pasangan'         => $pasangan->nik,
            'umur_pasangan'        => $nasabah->tanggal_lahir? Carbon::parse($pasangan->tgl_lahir)->age: null,
            'alamat_pasangan'      => $pasangan->alamat,
            'no_hp_pasangan'       => $pasangan->no_hp,
            
            
            // PEKERJAAN PASANGAN
            'alamat_usaha_pasangan'=> optional($pekerjaanpasangan)->alamat_usaha,

            //PENJAMIN
            'nama_penjamin'        => $penjamin->nama,
            'nik_penjamin'         => $penjamin->nik,
            'alamat_penjamin'      => $penjamin->alamat,
            'no_hp_penjamin'       => $penjamin->no_hp,

            // PEMBIAYAAN
            'nomor_pembiayaan'     => $pembiayaan->nomor_pembiayaan,
            'plafond'              => number_format($pembiayaan->plafond,0,',','.'),
            'plafond_raw'          => $pembiayaan->plafond,
            'plafond_terbilang'    => $this->terbilang($pembiayaan->plafond),
            'tenor'                => $pembiayaan->tenor,
            'jenis_tenor'          => ucfirst($pembiayaan->jenis_tenor),
            'persen_bunga'         => $pembiayaan->persen_bunga,
            'administrasi'         => number_format($pembiayaan->biaya_administrasi,0,',','.'),
            'materai'              => number_format($pembiayaan->materai,0,',','.'),
            'biaya_survey'         => number_format($pembiayaan->biaya_survey,0,',','.'),
            'dana_diterima'        => number_format($pembiayaan->dana_diterima,0,',','.'),

            // JAMINAN 1
            'jaminan1_jenis'          => optional($jaminan1)->jenis_jaminan,
            'jaminan1_nama'           => optional($jaminan1)->nama_jaminan,
            'jaminan1_detail'         => optional($jaminan1)->detail_jaminan,
            'jaminan1_nilai'          => optional($jaminan1)->nilai_taksasi,
            
            // JAMINAN 2
            'jaminan2_jenis'          => optional($jaminan2)->jenis_jaminan,
            'jaminan2_nama'           => optional($jaminan2)->nama_jaminan,
            'jaminan_detail'         => optional($jaminan2)->detail_jaminan,
            'jaminan2_nilai'          => optional($jaminan2)->nilai_taksasi,

            // ANGSURAN
            'angsuran'             => optional($angsuranPertama)->total_angsuran,
            'angsuran_format'      => number_format(optional($angsuranPertama)->total_angsuran ?? 0,0,',','.'),
            'angsuran_terbilang'   => $this->terbilang(optional($angsuranPertama)->total_angsuran ?? 0),
            'tanggal_jatuh_tempo_pertama'=> optional(optional($angsuranPertama)->tanggal_jatuh_tempo)
                                        ? Carbon::parse($angsuranPertama->tanggal_jatuh_tempo)->format('d-m-Y')
                                        : null,
            'tgl_jatuh_tempo_pertama' => $angsuranPertama ? Carbon::parse($angsuranPertama->tanggal_jatuh_tempo)->format('d'): null,
            'bulan_jatuh_tempo_pertama' => $angsuranPertama? Carbon::parse($angsuranPertama->tanggal_jatuh_tempo)->translatedFormat('F'): null,
            'tahun_jatuh_tempo_pertama' => $angsuranPertama? Carbon::parse($angsuranPertama->tanggal_jatuh_tempo)->format('Y'): null,

            'tanggal_jatuh_tempo_terakhir'=> optional(optional($angsuranTerakhir)->tanggal_jatuh_tempo)
                                        ? Carbon::parse($angsuranTerakhir->tanggal_jatuh_tempo)->format('d-m-Y')
                                        : null,
            'tgl_jatuh_tempo_terakhir' => $angsuranTerakhir? Carbon::parse($angsuranTerakhir->tanggal_jatuh_tempo)->format('d'): null,
            'bulan_jatuh_tempo_terakhir' => $angsuranTerakhir? Carbon::parse($angsuranTerakhir->tanggal_jatuh_tempo)->translatedFormat('F'): null,
            'tahun_jatuh_tempo_terakhir' => $angsuranTerakhir? Carbon::parse($angsuranTerakhir->tanggal_jatuh_tempo)->format('Y'): null,

            // MARKETING
            'marketing'            => optional(optional($pengajuan->marketing)->user)->name,

            //SURVEY
            'tanggal_survey'       => optional(optional($pengajuan->survey)->submitted_at)
                                        ? Carbon::parse($pengajuan->survey->submitted_at)->format('d-m-Y'): null,

            'potongan_kredit' => optional($angsuranPertama)->total_angsuran,
            'potongan_kredit_format' => number_format(optional($angsuranPertama)->total_angsuran ?? 0,0,',','.'),
            'potongan_kredit_terbilang' => $this->terbilang(optional($angsuranPertama)->total_angsuran ?? 0),
        ];

    }

    /**
     * Konversi angka menjadi huruf.
     * (sementara)
     */
    protected function terbilang($nilai): string
    {
        return trim(ucwords($nilai));
    }

}