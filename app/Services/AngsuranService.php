<?php

namespace App\Services;

use App\Models\Pembiayaan;
use Carbon\Carbon;

class AngsuranService
{
    /**
     * Generate seluruh jadwal angsuran
     */
    public function generate(Pembiayaan $pembiayaan): array
    {
        if ($pembiayaan->jenis_tenor == 'pendek') {
            return $this->generatePendek($pembiayaan);
        }

        return $this->generatePanjang($pembiayaan);
    }

    /**
     * Pembulatan ke atas kelipatan Rp1.000
     */
    protected function roundRibuan($nominal): int
    {
        return ceil($nominal / 1000) * 1000;
    }

    /**
     * PT Ersada : 1 bulan = 30 hari
     */
    protected function tambah30Hari(Carbon $tanggal): Carbon
    {
        return $tanggal->copy()->addDays(30);
    }

    
    //  Tanggal akan tetap sama setiap bulan.
    
    protected function tambah1Bulan(Carbon $tanggal): Carbon
    {
        return $tanggal->copy()->addMonthNoOverflow();
    }

    /**
     * Generate tenor pendek
     */
    protected function generatePendek(Pembiayaan $pembiayaan): array
    {
        $jadwal = [];

        $tanggal = Carbon::parse(
            $pembiayaan->tanggal_jatuh_tempo_pertama
        );

        $bunga = $this->roundRibuan(
            $pembiayaan->plafond * ($pembiayaan->persen_bunga / 100)
        );

        $sisaPokok = $pembiayaan->plafond;

        for ($i = 1; $i <= $pembiayaan->tenor; $i++) {

            if ($i == $pembiayaan->tenor) {

                $pokok = $pembiayaan->plafond;

                $total = $pokok + $bunga;

                $sisaPokok = 0;

            } else {

                $pokok = 0;

                $total = $bunga;

            }

            $jadwal[] = [
                'angsuran_ke'           => $i,
                'tanggal_jatuh_tempo'   => $tanggal->copy(),
                'pokok_angsuran'        => $pokok,
                'bunga_angsuran'        => $bunga,
                'total_angsuran'        => $total,
                'sisa_pokok'            => $sisaPokok,
                'total_terbayar'        => 0,
                'sisa_tagihan'          => $total,
            ];

            // $tanggal = $this->tambah30Hari($tanggal);
            $tanggal = $this->tambah1Bulan($tanggal);
        }

        return $jadwal;
    }

    /**
     * Generate tenor panjang
     */
    protected function generatePanjang(Pembiayaan $pembiayaan): array
    {
        $jadwal = [];

        $tanggal = Carbon::parse(
            $pembiayaan->tanggal_jatuh_tempo_pertama
        );

        $bunga = $this->roundRibuan(
            $pembiayaan->plafond * ($pembiayaan->persen_bunga / 100)
        );

        $pokokNormal = $this->roundRibuan(
            $pembiayaan->plafond / $pembiayaan->tenor
        );

        $sisaPokok = $pembiayaan->plafond;

        for ($i = 1; $i <= $pembiayaan->tenor; $i++) {

            if ($i == $pembiayaan->tenor) {

                // angsuran terakhir disesuaikan
                $pokok = $sisaPokok;

            } else {

                $pokok = $pokokNormal;

            }

            $sisaPokok -= $pokok;
            $totalAngsuran = $pokok + $bunga;
            $jadwal[] = [
                'angsuran_ke'           => $i,
                'tanggal_jatuh_tempo'   => $tanggal->copy(),
                'pokok_angsuran'        => $pokok,
                'bunga_angsuran'        => $bunga,
                'total_angsuran'        => $totalAngsuran,
                'sisa_pokok'            => max($sisaPokok,0),
                'total_terbayar'        => 0,
                'sisa_tagihan'          => $totalAngsuran,
            ];

            // $tanggal = $this->tambah30Hari($tanggal);
            $tanggal = $this->tambah1Bulan($tanggal);
        }

        return $jadwal;
    }

}