<?php

namespace App\Services;

use App\Models\Angsuran;
use Carbon\Carbon;

class DendaService
{
    /**
     * Hitung denda keterlambatan angsuran.
     *
     * ATURAN ERSADA:
     *
     * Tenor pendek 1-5 bulan
     *   Denda = 0,2% per hari
     *
     * Tenor panjang >= 12 bulan
     *   Denda = 0,1% per hari
     *
     * Dasar denda = plafond pembiayaan.
     *
     * Batas bebas denda ditentukan oleh
     * pencairans.tgl_telat_bayar.
     *
     * 1 bulan = 30 hari.
     */
    public function hitung(
        Angsuran $angsuran,
        Carbon|string $tanggalBayar
    ): array {

        /*
        |--------------------------------------------------------------------------
        | PEMBIAYAAN
        |--------------------------------------------------------------------------
        */

        $pembiayaan = $angsuran->pembiayaan;

        if (!$pembiayaan) {
            throw new \Exception(
                'Data pembiayaan tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PENCAIRAN
        |--------------------------------------------------------------------------
        */

        $pencairan = $pembiayaan
            ->akad
            ->pencairan;

        if (!$pencairan) {
            throw new \Exception(
                'Data pencairan tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TANGGAL BAYAR
        |--------------------------------------------------------------------------
        */

        $tanggalBayar = $tanggalBayar instanceof Carbon
            ? $tanggalBayar->copy()
            : Carbon::parse($tanggalBayar);


        /*
        |--------------------------------------------------------------------------
        | TANGGAL JATUH TEMPO
        |--------------------------------------------------------------------------
        */

        $tanggalJatuhTempo = Carbon::parse(
            $angsuran->tanggal_jatuh_tempo
        );


        /*
        |--------------------------------------------------------------------------
        | TANGGAL BEBAS DENDA
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | Jatuh tempo       : 01-08-2026
        | tgl_telat_bayar   : 10
        |
        | Maka:
        |
        | 10-08-2026 = batas bebas denda
        |
        */

        $tglTelatBayar = (int) (
            $pencairan->tgl_telat_bayar ?? 10
        );

        /*
        | Pastikan tanggal minimal 1
        */

        $tglTelatBayar = max(
            1,
            min($tglTelatBayar, 28)
        );


        /*
        |--------------------------------------------------------------------------
        | BENTUK TANGGAL BATAS BEBAS DENDA
        |--------------------------------------------------------------------------
        */

        $batasBebasDenda = $tanggalJatuhTempo
            ->copy()
            ->day($tglTelatBayar);


        /*
        |--------------------------------------------------------------------------
        | HITUNG HARI TERLAMBAT
        |--------------------------------------------------------------------------
        |
        | Jika bayar <= batas bebas denda:
        | hari telat = 0
        |
        | Jika bayar setelah batas:
        | hitung selisih hari.
        |
        */

        $hariTelat = 0;

        if ($tanggalBayar->gt($batasBebasDenda)) {

            $hariTelat =
                $batasBebasDenda->diffInDays(
                    $tanggalBayar
                );

        }


        /*
        |--------------------------------------------------------------------------
        | PERSENTASE DENDA
        |--------------------------------------------------------------------------
        */

        if ($pembiayaan->jenis_tenor === 'pendek') {

            $persenDenda = 0.20;

        } else {

            $persenDenda = 0.10;

        }


        /*
        |--------------------------------------------------------------------------
        | DASAR DENDA
        |--------------------------------------------------------------------------
        |
        | Sesuai aturan yang kita tetapkan:
        |
        | Denda selalu berdasarkan PLAFOND.
        |
        */

        $dasarDenda = (int) $pembiayaan->plafond;


        /*
        |--------------------------------------------------------------------------
        | DENDA PER HARI
        |--------------------------------------------------------------------------
        */

        $dendaPerHari = round(
            $dasarDenda *
            ($persenDenda / 100)
        );


        /*
        |--------------------------------------------------------------------------
        | TOTAL DENDA
        |--------------------------------------------------------------------------
        */

        $denda = $dendaPerHari * $hariTelat;


        /*
        |--------------------------------------------------------------------------
        | ADMIN KETERLAMBATAN
        |--------------------------------------------------------------------------
        |
        | Kurang dari 30 hari:
        |   Rp0
        |
        | 30-59 hari:
        |   Rp15.000
        |
        | 60-89 hari:
        |   Rp30.000
        |
        | dst.
        |
        */

        $periodeAdmin = intdiv(
            $hariTelat,
            30
        );


        $adminKeterlambatan =
            $periodeAdmin * 15000;


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAMBAHAN
        |--------------------------------------------------------------------------
        */

        $totalTambahan =
            $denda +
            $adminKeterlambatan;


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            /*
            | Informasi dasar
            */

            'tanggal_jatuh_tempo' =>
                $tanggalJatuhTempo->format('Y-m-d'),

            'batas_bebas_denda' =>
                $batasBebasDenda->format('Y-m-d'),

            'tanggal_bayar' =>
                $tanggalBayar->format('Y-m-d'),


            /*
            | Keterlambatan
            */

            'hari_telat' =>
                $hariTelat,

            'periode_admin' =>
                $periodeAdmin,


            /*
            | Perhitungan
            */

            'dasar_denda' =>
                $dasarDenda,

            'persen_denda' =>
                $persenDenda,

            'denda_per_hari' =>
                $dendaPerHari,

            'denda' =>
                $denda,

            'admin_keterlambatan' =>
                $adminKeterlambatan,

            'total_tambahan' =>
                $totalTambahan,


            /*
            | Format Rupiah
            */

            'dasar_denda_format' =>
                $this->rupiah($dasarDenda),

            'denda_per_hari_format' =>
                $this->rupiah($dendaPerHari),

            'denda_format' =>
                $this->rupiah($denda),

            'admin_keterlambatan_format' =>
                $this->rupiah(
                    $adminKeterlambatan
                ),

            'total_tambahan_format' =>
                $this->rupiah(
                    $totalTambahan
                ),

        ];
    }


    /**
     * Hitung total tambahan setelah diskon.
     *
     * Diskon nanti hanya boleh diberikan
     * melalui mekanisme persetujuan pimpinan.
     */
    public function setelahDiskon(
        array $hasil,
        int $diskon = 0
    ): array {

        $diskon = max(
            0,
            min(
                $diskon,
                $hasil['total_tambahan']
            )
        );


        $totalSetelahDiskon =
            $hasil['total_tambahan']
            - $diskon;


        return [

            'denda' =>
                $hasil['denda'],

            'admin_keterlambatan' =>
                $hasil['admin_keterlambatan'],

            'diskon' =>
                $diskon,

            'total_tambahan' =>
                $totalSetelahDiskon,

            'denda_format' =>
                $this->rupiah(
                    $hasil['denda']
                ),

            'admin_keterlambatan_format' =>
                $this->rupiah(
                    $hasil['admin_keterlambatan']
                ),

            'diskon_format' =>
                $this->rupiah(
                    $diskon
                ),

            'total_tambahan_format' =>
                $this->rupiah(
                    $totalSetelahDiskon
                ),

        ];
    }


    /**
     * Format Rupiah.
     */
    protected function rupiah(int|float $nominal): string
    {
        return number_format(
            $nominal,
            0,
            ',',
            '.'
        );
    }
}