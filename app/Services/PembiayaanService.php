<?php

namespace App\Services;

class PembiayaanService
{
    /**
     * Hitung seluruh data pembiayaan
     *
     * @param float $plafond
     * @param int $tenor
     * @param float $materai
     * @param float $biayaSurvey
     * @return array
     */
    public function hitung(
        float $plafond,
        int $tenor,
        float $materai = 0,
        float $biayaSurvey = 0
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Jenis Tenor
        |--------------------------------------------------------------------------
        */

        if ($tenor <= 5) {

            $jenisTenor = 'pendek';
            $persenBunga = 6.00;

        } else {

            $jenisTenor = 'panjang';
            $persenBunga = 2.50;

        }

        /*
        |--------------------------------------------------------------------------
        | Administrasi
        |--------------------------------------------------------------------------
        */

        $persenAdministrasi = 3.00;

        $biayaAdministrasi = $plafond * ($persenAdministrasi / 100);

        /*
        |--------------------------------------------------------------------------
        | Dana diterima
        |--------------------------------------------------------------------------
        */

        $danaDiterima = $plafond - $biayaAdministrasi - $materai - $biayaSurvey;

        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return [

            'jenis_tenor' => $jenisTenor,

            'persen_bunga' => $persenBunga,

            'persen_administrasi' => $persenAdministrasi,

            'biaya_administrasi' => round($biayaAdministrasi,2),

            'materai' => round($materai,2),

            'biaya_survey' => round($biayaSurvey,2),

            'dana_diterima' => round($danaDiterima,2),

        ];
    }

    /**
     * Nomor Pembiayaan
     */
    public function generateNomor(): string
    {
        $tanggal = now();

        $prefix = 'PMB';

        $tahun = $tanggal->format('Y');

        $bulan = $tanggal->format('m');

        $last = \App\Models\Pembiayaan::whereYear('created_at', $tahun)
                    ->count() + 1;

        return sprintf(
            '%s/%s/%s/%05d',
            $prefix,
            $tahun,
            $bulan,
            $last
        );
    }

}