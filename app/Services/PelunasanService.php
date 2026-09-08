<?php

namespace App\Services;

use App\Models\Pelunasan;
use App\Models\Pembiayaan;
use App\Models\Angsuran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PelunasanService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected AuditTrailService $auditTrail
    ) {}

    /*
    |--------------------------------------------------------------------------
    | HITUNG PELUNASAN
    |--------------------------------------------------------------------------
    */

    public function hitungPelunasan(
        Pembiayaan $pembiayaan,
        string $tanggalPelunasan
    ): array {

        $tanggalPelunasan = Carbon::parse($tanggalPelunasan);

        $angsurans = $pembiayaan->angsurans()
            ->where('status', '!=', 'dibayar')
            ->orderBy('angsuran_ke')
            ->get();

        if ($angsurans->isEmpty()) {
            throw new \Exception(
                'Tidak ada angsuran yang dapat dilunasi.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TENOR PENDEK
        |--------------------------------------------------------------------------
        */

        if ($pembiayaan->jenis_tenor === 'pendek') {

            /*
             * Cari angsuran yang masih memiliki pokok.
             * Pada tenor pendek, pokok berada pada angsuran terakhir.
             */
            $angsuranPokok = $angsurans
                ->where('sisa_pokok', '>', 0)
                ->sortByDesc('angsuran_ke')
                ->first();

            $sisaPokok = $angsuranPokok
                ? $angsuranPokok->sisa_pokok
                : 0;

            /*
             * Bunga hanya dihitung sampai tanggal pelunasan.
             *
             * Jadi bunga bulan yang belum jatuh tempo
             * tidak ikut dibebankan.
             */
            $sisaBunga = $angsurans
                ->filter(function ($angsuran) use ($tanggalPelunasan) {

                    return Carbon::parse(
                        $angsuran->tanggal_jatuh_tempo
                    )->lte($tanggalPelunasan);

                })
                ->sum('bunga_angsuran');

            $denda = $angsurans->sum('denda');

            $totalSebelumDiskon =
                $sisaPokok +
                $sisaBunga +
                $denda;

            return [
                'jenis_tenor' => 'pendek',
                'sisa_angsuran' => $angsurans->count(),
                'sisa_pokok' => $sisaPokok,
                'sisa_bunga' => $sisaBunga,
                'denda' => $denda,
                'total_sebelum_diskon' => $totalSebelumDiskon,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | TENOR PANJANG
        |--------------------------------------------------------------------------
        */

        $sisaPokok = $angsurans->sum('pokok_angsuran');

        $sisaBunga = $angsurans->sum('bunga_angsuran');

        $denda = $angsurans->sum('denda');

        $totalSisaAngsuran =
            $angsurans->sum('total_angsuran');

        $totalSebelumDiskon =
            $totalSisaAngsuran +
            $denda;

        return [
            'jenis_tenor' => 'panjang',
            'sisa_angsuran' => $angsurans->count(),
            'sisa_pokok' => $sisaPokok,
            'sisa_bunga' => $sisaBunga,
            'denda' => $denda,
            'total_sebelum_diskon' => $totalSebelumDiskon,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | AJUKAN PELUNASAN
    |--------------------------------------------------------------------------
    */

    public function store(
        Pembiayaan $pembiayaan,
        array $data
    ): Pelunasan {

        return DB::transaction(function () use (
            $pembiayaan,
            $data
        ) {

            if ($pembiayaan->status === 'lunas') {

                throw new \Exception(
                    'Pembiayaan sudah lunas.'
                );
            }

            /*
             * Hitung nilai pelunasan.
             */
            $perhitungan = $this->hitungPelunasan(
                $pembiayaan,
                $data['tanggal_pelunasan']
            );

            $diskon = (int) ($data['diskon'] ?? 0);

            /*
             * Validasi diskon.
             */
            if ($diskon < 0) {

                throw new \Exception(
                    'Nilai diskon tidak valid.'
                );
            }

            if (
                $diskon >
                $perhitungan['total_sebelum_diskon']
            ) {

                throw new \Exception(
                    'Diskon tidak boleh lebih besar dari nilai pelunasan.'
                );
            }

            /*
             * Tentukan jenis pelunasan.
             */
            $jenisPelunasan =
                $diskon > 0
                    ? 'dengan_diskon'
                    : 'normal';

            /*
             * Normal langsung siap dibayar.
             *
             * Dengan diskon harus menunggu pimpinan.
             */
            $status =
                $diskon > 0
                    ? 'menunggu_persetujuan'
                    : 'siap_dibayar';

            /*
             * Untuk normal:
             *
             * total = total normal
             *
             * Untuk pengajuan diskon:
             *
             * diskon masih merupakan DISKON YANG DIAJUKAN.
             *
             * Belum dianggap sebagai diskon final.
             */
            $totalPelunasan =
                $perhitungan['total_sebelum_diskon'];

            $pelunasan = Pelunasan::create([

                'pembiayaan_id' =>
                    $pembiayaan->id,

                'nomor_pelunasan' =>
                    $this->paymentService
                        ->generateNumber('PLN-'),

                'tanggal_pelunasan' =>
                    $data['tanggal_pelunasan'],

                'jenis_pelunasan' =>
                    $jenisPelunasan,

                'status' =>
                    $status,

                'sisa_pokok' =>
                    $perhitungan['sisa_pokok'],

                'sisa_bunga' =>
                    $perhitungan['sisa_bunga'],

                'denda' =>
                    $perhitungan['denda'],

                'total_sebelum_diskon' =>
                    $perhitungan['total_sebelum_diskon'],

                /*
                 * Diskon yang DIMINTA.
                 */
                'diskon' =>
                    $diskon,

                /*
                 * Normal:
                 * tidak perlu approval sehingga diskon final = 0.
                 *
                 * Dengan diskon:
                 * null sampai pimpinan menyetujui.
                 */
                'diskon_disetujui' =>
                    $diskon > 0
                        ? null
                        : 0,

                /*
                 * Untuk sementara total akhir masih
                 * menggunakan total normal.
                 *
                 * Jika approval diberikan,
                 * nilai ini akan dihitung ulang.
                 */
                'total_pelunasan' =>
                    $totalPelunasan,

                'alasan_diskon' =>
                    $data['alasan_diskon'] ?? null,

                'keterangan' =>
                    $data['keterangan'] ?? null,

                'created_by' =>
                    auth()->id(),
            ]);

            /*
             * Audit Trail
             */
            $this->auditTrail->log(
                $pembiayaan,
                'Pelunasan',
                'Pengajuan Pelunasan',
                'Nomor Pelunasan : ' .
                $pelunasan->nomor_pelunasan .
                ' | Total : Rp ' .
                number_format(
                    $pelunasan->total_sebelum_diskon,
                    0,
                    ',',
                    '.'
                ) .
                ' | Diskon Diajukan : Rp ' .
                number_format(
                    $pelunasan->diskon,
                    0,
                    ',',
                    '.'
                )
            );

            return $pelunasan;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE PELUNASAN DENGAN DISKON
    |--------------------------------------------------------------------------
    */

    public function approve(
        Pelunasan $pelunasan,
        array $data
    ): Pelunasan {

        return DB::transaction(function () use (
            $pelunasan,
            $data
        ) {

            /*
            |--------------------------------------------------------------------------
            | Validasi status
            |--------------------------------------------------------------------------
            */

            if ($pelunasan->status !== 'menunggu_persetujuan') {

                throw new \Exception(
                    'Pelunasan tidak sedang menunggu persetujuan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan memang pelunasan dengan diskon
            |--------------------------------------------------------------------------
            */

            if ($pelunasan->jenis_pelunasan !== 'dengan_diskon') {

                throw new \Exception(
                    'Pelunasan ini tidak memerlukan persetujuan diskon.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Ambil diskon yang disetujui
            |--------------------------------------------------------------------------
            */

            $diskonDisetujui = (int) (
                $data['diskon_disetujui']
                ?? $pelunasan->diskon
            );

            /*
            |--------------------------------------------------------------------------
            | Validasi diskon
            |--------------------------------------------------------------------------
            */

            if ($diskonDisetujui < 0) {

                throw new \Exception(
                    'Nilai diskon disetujui tidak valid.'
                );
            }

            if (
                $diskonDisetujui >
                $pelunasan->total_sebelum_diskon
            ) {

                throw new \Exception(
                    'Diskon yang disetujui tidak boleh lebih besar dari total pelunasan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Hitung total akhir
            |--------------------------------------------------------------------------
            */

            $totalPelunasan =
                $pelunasan->total_sebelum_diskon
                - $diskonDisetujui;

            /*
            |--------------------------------------------------------------------------
            | Update approval
            |--------------------------------------------------------------------------
            */

            $pelunasan->update([

                'status' =>
                    'siap_dibayar',

                'diskon_disetujui' =>
                    $diskonDisetujui,

                'total_pelunasan' =>
                    $totalPelunasan,

                'approved_by' =>
                    auth()->id(),

                'approved_at' =>
                    now(),

                'catatan_approval' =>
                    $data['catatan_approval'] ?? null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Audit Trail
            |--------------------------------------------------------------------------
            */

            $this->auditTrail->log(
                $pelunasan->pembiayaan,
                'Pelunasan',
                'Persetujuan Pelunasan',
                'Nomor Pelunasan : ' .
                $pelunasan->nomor_pelunasan .
                ' | Diskon Diajukan : Rp ' .
                number_format(
                    $pelunasan->diskon,
                    0,
                    ',',
                    '.'
                ) .
                ' | Diskon Disetujui : Rp ' .
                number_format(
                    $diskonDisetujui,
                    0,
                    ',',
                    '.'
                ) .
                ' | Total Pelunasan : Rp ' .
                number_format(
                    $totalPelunasan,
                    0,
                    ',',
                    '.'
                )
            );

            return $pelunasan->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT PELUNASAN DENGAN DISKON
    |--------------------------------------------------------------------------
    */

    public function reject(
        Pelunasan $pelunasan,
        array $data
    ): Pelunasan {

        return DB::transaction(function () use (
            $pelunasan,
            $data
        ) {

            /*
            |--------------------------------------------------------------------------
            | Validasi status
            |--------------------------------------------------------------------------
            */

            if ($pelunasan->status !== 'menunggu_persetujuan') {

                throw new \Exception(
                    'Pelunasan tidak sedang menunggu persetujuan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan memang pelunasan dengan diskon
            |--------------------------------------------------------------------------
            */

            if ($pelunasan->jenis_pelunasan !== 'dengan_diskon') {

                throw new \Exception(
                    'Pelunasan ini tidak memerlukan persetujuan diskon.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update menjadi ditolak
            |--------------------------------------------------------------------------
            */

            $pelunasan->update([

                'status' =>
                    'ditolak',

                'approved_by' =>
                    auth()->id(),

                'approved_at' =>
                    now(),

                'catatan_approval' =>
                    $data['catatan_approval'] ?? null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Audit Trail
            |--------------------------------------------------------------------------
            */

            $this->auditTrail->log(
                $pelunasan->pembiayaan,
                'Pelunasan',
                'Penolakan Pelunasan',
                'Nomor Pelunasan : ' .
                $pelunasan->nomor_pelunasan .
                ' | Diskon Diajukan : Rp ' .
                number_format(
                    $pelunasan->diskon,
                    0,
                    ',',
                    '.'
                ) .
                ' | Alasan : ' .
                ($data['catatan_approval'] ?? '-')
            );

            return $pelunasan->fresh();
        });
    }    

    /*
    |--------------------------------------------------------------------------
    | BAYAR PELUNASAN
    |--------------------------------------------------------------------------
    */
    public function bayar(Pelunasan $pelunasan, array $data): Pelunasan
    {
        return DB::transaction(function () use ($pelunasan, $data) {

            // Lock pelunasan
            $pelunasan = Pelunasan::query()
                ->whereKey($pelunasan->id)
                ->lockForUpdate()
                ->firstOrFail();

            // ==============================
            // VALIDASI STATUS
            // ==============================

            if ($pelunasan->status !== 'siap_dibayar') {
                throw new \Exception(
                    'Pelunasan belum siap untuk dibayar.'
                );
            }

            // ==============================
            // VALIDASI JUMLAH BAYAR
            // ==============================

            $jumlahBayar = (int) $data['jumlah_bayar'];

            if ($jumlahBayar <= 0) {
                throw new \Exception(
                    'Jumlah pembayaran harus lebih dari 0.'
                );
            }

            // Untuk pelunasan kita wajib bayar
            // tepat sebesar total pelunasan.
            if ($jumlahBayar != (int) $pelunasan->total_pelunasan) {
                throw new \Exception(
                    'Jumlah pembayaran harus sesuai dengan total pelunasan.'
                );
            }

            // ==============================
            // LOCK PEMBIAYAAN
            // ==============================

            $pembiayaan = Pembiayaan::query()
                ->whereKey($pelunasan->pembiayaan_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pembiayaan->status === 'lunas') {
                throw new \Exception(
                    'Pembiayaan sudah berstatus lunas.'
                );
            }

            // ==============================
            // LOCK ANGSURAN
            // ==============================

            $angsurans = Angsuran::query()
                ->where('pembiayaan_id', $pembiayaan->id)
                ->where('status', '!=', 'dibayar')
                ->whereNull('pelunasan_id')
                ->orderBy('angsuran_ke')
                ->lockForUpdate()
                ->get();

            if ($angsurans->isEmpty()) {
                throw new \Exception(
                    'Tidak ada angsuran yang masih terbuka.'
                );
            }

            // ==============================
            // TANGGAL BAYAR
            // ==============================

            $tanggalBayar = $data['tanggal_bayar']
                ?? now()->toDateString();

            // ==============================
            // UPDATE PELUNASAN
            // ==============================

            $pelunasan->update([
                'status'   => 'dibayar',
                'paid_by'  => auth()->id(),
                'paid_at'  => now(),
            ]);

            // ==============================
            // TUTUP SELURUH ANGSURAN
            // ==============================
            //
            // PENTING:
            // Kita TIDAK mengubah jumlah_bayar /
            // total_terbayar menjadi total normal
            // angsuran karena bisa ada diskon.
            //
            // Pelunasan sebenarnya dicatat di tabel
            // pelunasans.
            //

            foreach ($angsurans as $angsuran) {

                $angsuran->update([
                    'pelunasan_id'  => $pelunasan->id,
                    'status'       => 'dibayar',
                    'tanggal_bayar' => $tanggalBayar,
                    'sisa_tagihan' => 0,
                    'sisa_pokok'   => 0,
                ]);
            }

            // ==============================
            // PEMBIAYAAN LUNAS
            // ==============================

            $pembiayaan->update([
                'status' => 'lunas',
            ]);

            // ==============================
            // AUDIT TRAIL
            // ==============================

            // Jika project Anda sudah mempunyai
            // mekanisme audit yang digunakan di
            // PelunasanService sebelumnya,
            // letakkan audit pembayaran di sini.

            return $pelunasan->fresh([
                'pembiayaan',
                'creator',
                'approver',
                'payer',
            ]);
        });
    }
}