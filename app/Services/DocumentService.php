<?php

namespace App\Services;

use App\Models\Pengajuan;
use Illuminate\Support\Collection;

class DocumentService
{
    /**
     * Menghasilkan daftar dokumen yang harus ditampilkan
     */
    public function getDocuments(Pengajuan $pengajuan): array
    {
        $required = [];
        $optional = [];

        $nasabah = $pengajuan->nasabah;

        if (!$nasabah) {
            return [
                'required' => [],
                'optional' => [],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DATA NASABAH
        |--------------------------------------------------------------------------
        */

        $statusPerkawinan = strtolower(
            trim($nasabah->status_perkawinan ?? '')
        );

        $kategori = strtolower(
            trim($pengajuan->kategori_nasabah ?? '')
        );


        /*
        |--------------------------------------------------------------------------
        | DOKUMEN WAJIB SEMUA NASABAH
        |--------------------------------------------------------------------------
        */

        $this->add(
            $required,
            'kk',
            'Kartu Keluarga'
        );


        /*
        |--------------------------------------------------------------------------
        | NASABAH MENIKAH
        |--------------------------------------------------------------------------
        */

        if ($statusPerkawinan === 'menikah') {

            /*
            | KTP pasangan wajib
            */

            $this->add(
                $required,
                'ktp_pasangan',
                'KTP Pasangan'
            );


            /*
            | Buku Nikah optional
            */

            $this->add(
                $optional,
                'buku_nikah',
                'Buku Nikah'
            );


            /*
            | Akte Kelahiran optional
            */

            $this->add(
                $optional,
                'akte_kelahiran',
                'Akte Kelahiran'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYROL
        |--------------------------------------------------------------------------
        */

        if ($kategori === 'payrol') {

            /*
            | ATM & Buku Tabungan
            */

            $this->add(
                $required,
                'atm',
                'ATM & Buku Tabungan'
            );


            /*
            | BPJS Ketenagakerjaan
            */

            $this->add(
                $required,
                'bpjs',
                'BPJS Ketenagakerjaan'
            );


            /*
            | SK Kerja
            */

            $this->add(
                $required,
                'sk_kerja',
                'SK Kerja'
            );
        }


        return [
            'required' => $required,
            'optional' => $optional,
        ];
    }

    /**
     * Mengambil dokumen yang sudah diupload
     */
    public function getUploadedDocuments(Pengajuan $pengajuan): Collection
    {
        return $pengajuan
            ->dokumenPengajuans()
            ->get()
            ->keyBy('jenis_dokumen');
    }

    /**
     * Menghasilkan daftar dokumen yang belum diupload
     */
   public function getMissingDocuments(Pengajuan $pengajuan): array
    {
        $documents = $this->getDocuments($pengajuan);

        $uploaded = $this->getUploadedDocuments($pengajuan);

        $missing = [];


        foreach ($documents['required'] as $doc) {

            if (!$uploaded->has($doc['code'])) {

                $missing[] = $doc;
            }
        }


        return $missing;
    }

    /**
     * Helper menambahkan dokumen
     */
    private function add(array &$target, string $code, string $label): void
    {
        $target[] = [
            'code' => $code,
            'label' => $label,
        ];
    }
}