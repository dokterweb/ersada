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
        $oneOf = [];
        $optional = [];

        $nasabah = $pengajuan->nasabah;
        $pekerjaan = optional($nasabah->pekerjaanNasabah);

        $statusPernikahan = strtolower($nasabah->status_pernikahan ?? '');
        $jumlahAnak = (int) ($nasabah->jumlah_anak ?? 0);

        // Sesuaikan dengan field pada tabel pengajuan Anda
        $kategori = strtolower($pengajuan->kategori_nasabah ?? '');

        // Sesuaikan dengan field pada tabel pekerjaan_nasabahs Anda
        $jenisPekerjaan = strtolower($pekerjaan->jenis_pekerjaan ?? '');

        /*
        |--------------------------------------------------------------------------
        | Dokumen Wajib Semua Nasabah
        |--------------------------------------------------------------------------
        */

        $this->add($required, 'ktp', 'KTP');
        $this->add($required, 'kk', 'Kartu Keluarga');

        /*
        |--------------------------------------------------------------------------
        | Status Pernikahan
        |--------------------------------------------------------------------------
        */

        if ($statusPernikahan === 'menikah') {

            $this->add($required, 'ktp_pasangan', 'KTP Suami / Istri');
            $this->add($required, 'buku_nikah', 'Buku Nikah');

            if ($jumlahAnak > 0) {
                $this->add($optional, 'akta_anak', 'Akta Kelahiran Anak');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Payroll
        |--------------------------------------------------------------------------
        */

        if ($kategori === 'payroll') {

            $this->add($required, 'atm', 'ATM & Buku Tabungan');
            $this->add($required, 'bpjs', 'BPJS Ketenagakerjaan');
            $this->add($required, 'sk_kerja', 'SK Kerja');
        }

        /*
        |--------------------------------------------------------------------------
        | Jenis Pekerjaan
        |--------------------------------------------------------------------------
        */

        switch ($jenisPekerjaan) {

            case 'asn':

                $this->add($required, 'sk_asn', 'SK ASN');

                break;

            case 'pegawai swasta':

                $this->add($required, 'slip_gaji', 'Slip Gaji');

                break;

            case 'wiraswasta':

                $this->add($required, 'sku', 'Surat Keterangan Usaha');
                $this->add($required, 'foto_usaha', 'Foto Tempat Usaha');
                $this->add($required, 'rekening_usaha', 'Rekening Usaha');

                break;

            case 'petani':

                $this->add($required, 'foto_kebun', 'Foto Kebun');
                $this->add($required, 'surat_lahan', 'Surat Kepemilikan Lahan');

                break;

            case 'nelayan':

                $this->add($required, 'foto_kapal', 'Foto Kapal');
                $this->add($required, 'surat_kapal', 'Surat Kapal');

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Salah satu wajib dipilih
        |--------------------------------------------------------------------------
        */

        $this->add($oneOf, 'bpkb', 'BPKB');
        $this->add($oneOf, 'surat_tanah', 'Surat Tanah');

        /*
        |--------------------------------------------------------------------------
        | Optional
        |--------------------------------------------------------------------------
        */

        $this->add($optional, 'foto_rumah', 'Foto Rumah');
        $this->add($optional, 'ijazah', 'Ijazah Terakhir');

        return [
            'required' => $required,
            'one_of' => $oneOf,
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

        // Minimal salah satu jaminan harus ada
        $hasOneOf = false;

        foreach ($documents['one_of'] as $doc) {

            if ($uploaded->has($doc['code'])) {
                $hasOneOf = true;
                break;
            }
        }

        if (!$hasOneOf && count($documents['one_of'])) {

            $missing[] = [
                'code' => 'jaminan',
                'label' => 'Minimal salah satu dokumen jaminan (BPKB / Surat Tanah)'
            ];
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