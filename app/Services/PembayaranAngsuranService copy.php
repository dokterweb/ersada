<?php

namespace App\Services;

use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranAngsuranService
{
    protected AuditTrailService $auditTrail;

    public function __construct(
        AuditTrailService $auditTrail
    ) {
        $this->auditTrail = $auditTrail;
    }


    /*
    |--------------------------------------------------------------------------
    | PREVIEW DENDA
    |--------------------------------------------------------------------------
    |
    | Digunakan ketika user membuka modal pembayaran
    | dan ketika tanggal bayar diubah.
    |
    */

    public function previewDenda(
        Angsuran $angsuran,
        string|Carbon $tanggalBayar
    ): array {

        $angsuran->load([
            'pembiayaan.akad.pencairan',
        ]);

        $tanggalBayar = Carbon::parse(
            $tanggalBayar
        )->startOfDay();


        /*
        |--------------------------------------------------------------------------
        | TANGGAL JATUH TEMPO
        |--------------------------------------------------------------------------
        */

        $jatuhTempo = Carbon::parse(
            $angsuran->tanggal_jatuh_tempo
        )->startOfDay();


        /*
        |--------------------------------------------------------------------------
        | TANGGAL TELAT BAYAR
        |--------------------------------------------------------------------------
        |
        | Default berasal dari pencairan.
        | Misalnya:
        |
        | jatuh tempo       = 01-08
        | tgl_telat_bayar   = 10
        |
        | maka tanggal mulai denda = 10-08.
        |
        */

        $tglTelatBayar = optional(
            $angsuran
                ->pembiayaan
                ->akad
                ->pencairan
        )->tgl_telat_bayar;


        /*
        |--------------------------------------------------------------------------
        | JIKA TIDAK ADA SETTING
        |--------------------------------------------------------------------------
        |
        | Default 10.
        |
        */

        $tglTelatBayar =
            $tglTelatBayar
            ?: 10;


        /*
        |--------------------------------------------------------------------------
        | TENTUKAN TANGGAL MULAI DENDA
        |--------------------------------------------------------------------------
        */

        $tanggalMulaiDenda =
            $jatuhTempo->copy()
                ->day($tglTelatBayar);


        /*
        |--------------------------------------------------------------------------
        | JIKA NOMOR TANGGAL TIDAK VALID
        |--------------------------------------------------------------------------
        |
        | Contoh jatuh tempo 31 tetapi bulan berikutnya
        | hanya memiliki 30 hari.
        |
        */

        if (
            $tanggalMulaiDenda->month
            !==
            $jatuhTempo->month
        ) {

            $tanggalMulaiDenda =
                $jatuhTempo
                    ->copy()
                    ->endOfMonth();
        }


        /*
        |--------------------------------------------------------------------------
        | HARI TERLAMBAT
        |--------------------------------------------------------------------------
        |
        | Bayar pada tanggal telat bayar:
        |
        | 10 = 0 hari
        |
        | Bayar 11:
        | 1 hari
        |
        */

        if (
            $tanggalBayar
                ->lt($tanggalMulaiDenda)
        ) {

            $hariTerlambat = 0;

        } else {

            $hariTerlambat =
                $tanggalMulaiDenda
                    ->diffInDays(
                        $tanggalBayar
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | TENTUKAN RATE DENDA
        |--------------------------------------------------------------------------
        |
        | TENOR PANJANG = 0,1%
        | TENOR PENDEK  = 0,2%
        |
        */

        $jenisTenor =
            $angsuran
                ->pembiayaan
                ->jenis_tenor;


        if (
            $jenisTenor === 'pendek'
        ) {

            $persenDenda =
                0.20;

        } else {

            $persenDenda =
                0.10;
        }


        /*
        |--------------------------------------------------------------------------
        | DENDA PER HARI
        |--------------------------------------------------------------------------
        */

        $dendaPerHari =
            $angsuran
                ->pembiayaan
                ->plafond
            *
            ($persenDenda / 100);


        /*
        |--------------------------------------------------------------------------
        | TOTAL DENDA
        |--------------------------------------------------------------------------
        */

        $denda =
            round(
                $dendaPerHari
                *
                $hariTerlambat
            );


        /*
        |--------------------------------------------------------------------------
        | ADMIN KETERLAMBATAN
        |--------------------------------------------------------------------------
        |
        | Setiap 30 hari keterlambatan:
        |
        | Rp15.000
        |
        */

        $bulanTerlambat =
            intdiv(
                $hariTerlambat,
                30
            );


        $adminKeterlambatan =
            $bulanTerlambat
            *
            15000;


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAMBAHAN
        |--------------------------------------------------------------------------
        */

        $totalTambahan =
            $denda
            +
            $adminKeterlambatan;


        return [

            'hari_terlambat' =>
                $hariTerlambat,

            'persen_denda' =>
                $persenDenda,

            'denda_per_hari' =>
                round(
                    $dendaPerHari
                ),

            'denda' =>
                $denda,

            'admin_keterlambatan' =>
                $adminKeterlambatan,

            'total_tambahan' =>
                $totalTambahan,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | BAYAR
    |--------------------------------------------------------------------------
    */

public function bayar(
    Angsuran $angsuran,
    array $data
): array {

    return DB::transaction(function () use (
        $angsuran,
        $data
    ) {

        /*
        |--------------------------------------------------------------------------
        | LOCK ANGSURAN
        |--------------------------------------------------------------------------
        */

        $angsuran = Angsuran::where(
            'id',
            $angsuran->id
        )
        ->lockForUpdate()
        ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | CEK STATUS
        |--------------------------------------------------------------------------
        */

        if ($angsuran->status === 'dibayar') {

            throw new \Exception(
                'Angsuran ini sudah lunas.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | JUMLAH BAYAR
        |--------------------------------------------------------------------------
        |
        | Ini adalah total uang yang dibayarkan nasabah.
        |
        */

        $jumlahBayar = (int) (
            $data['jumlah_bayar'] ?? 0
        );


        if ($jumlahBayar <= 0) {

            throw new \Exception(
                'Nominal pembayaran harus lebih besar dari nol.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG DENDA SERVER
        |--------------------------------------------------------------------------
        */

        $preview = $this->previewDenda(
            $angsuran,
            $data['tanggal_bayar']
        );


        $denda = (int) (
            $preview['denda'] ?? 0
        );


        $adminKeterlambatan = (int) (
            $preview['admin_keterlambatan'] ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | DISKON
        |--------------------------------------------------------------------------
        */

        $diskon = (int) (
            $data['diskon'] ?? 0
        );


        if ($diskon < 0) {
            $diskon = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI DISKON
        |--------------------------------------------------------------------------
        */

        $totalTambahanSebelumDiskon =
            $denda
            +
            $adminKeterlambatan;


        if (
            $diskon >
            $totalTambahanSebelumDiskon
        ) {

            throw new \Exception(
                'Diskon tidak boleh melebihi total denda dan admin keterlambatan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAMBAHAN
        |--------------------------------------------------------------------------
        */

        $totalTambahan =
            $denda
            +
            $adminKeterlambatan
            -
            $diskon;


        /*
        |--------------------------------------------------------------------------
        | SISA TAGIHAN ANGSURAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan =
            max(
                0,
                (int) $angsuran->sisa_tagihan
            );


        /*
        |--------------------------------------------------------------------------
        | TOTAL KEWAJIBAN
        |--------------------------------------------------------------------------
        */

        $totalKewajiban =
            $sisaTagihan
            +
            $totalTambahan;


        /*
        |--------------------------------------------------------------------------
        | VALIDASI JUMLAH PEMBAYARAN
        |--------------------------------------------------------------------------
        |
        | Sekarang pembayaran boleh lebih kecil
        | dari total kewajiban.
        |
        | Tetapi tidak boleh melebihi:
        |
        | sisa angsuran + denda + admin
        |
        */

        if (
            $jumlahBayar >
            $totalKewajiban
        ) {

            throw new \Exception(
                'Nominal pembayaran melebihi total kewajiban Rp ' .
                number_format(
                    $totalKewajiban,
                    0,
                    ',',
                    '.'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ALOKASI PEMBAYARAN
        |--------------------------------------------------------------------------
        |
        | Urutan:
        |
        | 1. Denda + Admin
        | 2. Sisa masuk ke angsuran
        |
        */

        $pembayaranUntukTambahan =
            min(
                $jumlahBayar,
                $totalTambahan
            );


        /*
        |--------------------------------------------------------------------------
        | SISA UANG SETELAH DENDA / ADMIN
        |--------------------------------------------------------------------------
        */

        $pembayaranUntukAngsuran =
            max(
                0,
                $jumlahBayar
                -
                $pembayaranUntukTambahan
            );


        /*
        |--------------------------------------------------------------------------
        | BATASI PEMBAYARAN ANGSURAN
        |--------------------------------------------------------------------------
        */

        $pembayaranUntukAngsuran =
            min(
                $pembayaranUntukAngsuran,
                $sisaTagihan
            );


        /*
        |--------------------------------------------------------------------------
        | TOTAL DENDA YANG BENAR-BENAR TERBAYAR
        |--------------------------------------------------------------------------
        |
        | Karena user bisa membayar sebagian.
        |
        */

        $dendaTerbayar =
            min(
                $pembayaranUntukTambahan,
                $denda
            );


        /*
        |--------------------------------------------------------------------------
        | SISA UNTUK ADMIN
        |--------------------------------------------------------------------------
        */

        $sisaSetelahDenda =
            max(
                0,
                $pembayaranUntukTambahan
                -
                $dendaTerbayar
            );


        $adminTerbayar =
            min(
                $sisaSetelahDenda,
                $adminKeterlambatan
            );


        /*
        |--------------------------------------------------------------------------
        | CATATAN DISKON
        |--------------------------------------------------------------------------
        |
        | Untuk sekarang diskon dianggap sebagai
        | pengurang kewajiban tambahan.
        |
        */

        $dendaFinal =
            max(
                0,
                $denda - $diskon
            );


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PEMBAYARAN
        |--------------------------------------------------------------------------
        |
        | Kita simpan nominal denda/admin yang menjadi
        | kewajiban transaksi ini.
        |
        */

        $pembayaran = PembayaranAngsuran::create([

            'angsuran_id' =>
                $angsuran->id,

            'nomor_pembayaran' =>
                $this->generateNomor(),

            'tanggal_bayar' =>
                $data['tanggal_bayar'],

            /*
             * Jumlah uang yang dibayar nasabah.
             */
            'jumlah_bayar' =>
                $jumlahBayar,

            /*
             * Denda transaksi.
             */
            'denda' =>
                $dendaFinal,

            /*
             * Diskon.
             */
            'diskon' =>
                $diskon,

            /*
             * Admin keterlambatan.
             */
            'admin_keterlambatan' =>
                $adminKeterlambatan,

            /*
             * Total uang yang diterima.
             */
            'total_dibayar' =>
                $jumlahBayar,

            'metode' =>
                $data['metode'],

            'keterangan' =>
                $data['keterangan'] ?? null,

            'created_by' =>
                Auth::id(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMBAYARAN YANG MASUK KE ANGSURAN
        |--------------------------------------------------------------------------
        |
        | JANGAN menggunakan SUM(jumlah_bayar)
        | karena jumlah_bayar sekarang adalah
        | total uang yang dibayar nasabah.
        |
        | Kita hitung setiap transaksi:
        |
        | jumlah_bayar
        | - denda
        | - admin
        | + diskon
        |
        */

        $totalTerbayar = 0;


        foreach (
            $angsuran
                ->pembayaranAngsurans()
                ->get()
            as $pembayaranItem
        ) {

            $bagianAngsuran =
                max(
                    0,
                    (int) $pembayaranItem->jumlah_bayar
                    -
                    (int) $pembayaranItem->denda
                    -
                    (int) $pembayaranItem->admin_keterlambatan
                    +
                    (int) $pembayaranItem->diskon
                );


            $totalTerbayar +=
                min(
                    $bagianAngsuran,
                    $angsuran->total_angsuran
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan =
            max(
                0,
                (int) $angsuran->total_angsuran
                -
                $totalTerbayar
            );


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        $status =
            $angsuran->status;


        $tanggalLunas = null;


        if (
            $sisaTagihan == 0
        ) {

            $status =
                'dibayar';

            $tanggalLunas =
                $data['tanggal_bayar'];

        } else {

            $tanggalBayar =
                Carbon::parse(
                    $data['tanggal_bayar']
                );

            $jatuhTempo =
                Carbon::parse(
                    $angsuran->tanggal_jatuh_tempo
                );


            if (
                $tanggalBayar->gte(
                    $jatuhTempo
                )
            ) {

                $status =
                    'jatuh_tempo';

            } else {

                $status =
                    'belum_jatuh_tempo';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE ANGSURAN
        |--------------------------------------------------------------------------
        */

        $angsuran->update([

            'status' =>
                $status,

            'tanggal_bayar' =>
                $tanggalLunas,

            /*
             * Denda terakhir yang dihitung.
             */
            'denda' =>
                $dendaFinal,

            'total_terbayar' =>
                $totalTerbayar,

            'sisa_tagihan' =>
                $sisaTagihan,
        ]);


        /*
        |--------------------------------------------------------------------------
        | SUMMARY PEMBIAYAAN
        |--------------------------------------------------------------------------
        */

        $angsurans =
            $angsuran
                ->pembiayaan
                ->angsurans()
                ->orderBy(
                    'angsuran_ke'
                )
                ->get();


        $totalAngsuran =
            $angsurans->count();


        $sudahDibayar =
            $angsurans
                ->where(
                    'status',
                    'dibayar'
                )
                ->count();


        $sisaAngsuran =
            $totalAngsuran
            -
            $sudahDibayar;


        $outstanding =
            $angsurans
                ->where(
                    'status',
                    '!=',
                    'dibayar'
                )
                ->sortBy(
                    'angsuran_ke'
                )
                ->first()
                ?->sisa_pokok
                ??
                0;


        /*
        |--------------------------------------------------------------------------
        | AUDIT
        |--------------------------------------------------------------------------
        */

        $this->auditTrail->log(

            $angsuran->pembiayaan,

            'Angsuran',

            'Pembayaran Angsuran',

            'Angsuran Ke-' .
            $angsuran->angsuran_ke .

            ' | Bayar : Rp ' .
            number_format(
                $jumlahBayar,
                0,
                ',',
                '.'
            ) .

            ' | Denda : Rp ' .
            number_format(
                $dendaFinal,
                0,
                ',',
                '.'
            ) .

            ' | Admin : Rp ' .
            number_format(
                $adminKeterlambatan,
                0,
                ',',
                '.'
            ) .

            ' | Untuk Angsuran : Rp ' .
            number_format(
                $pembayaranUntukAngsuran,
                0,
                ',',
                '.'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            'pembayaran' =>
                $pembayaran,


            'row' => [

                'id' =>
                    $angsuran->id,

                'status' =>
                    ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $status
                        )
                    ),

                'badge' =>
                    match ($status) {

                        'dibayar' =>
                            'bg-success',

                        'jatuh_tempo' =>
                            'bg-danger',

                        default =>
                            'bg-secondary',
                    },


                'total_terbayar' =>
                    $totalTerbayar,

                'total_terbayar_format' =>
                    number_format(
                        $totalTerbayar,
                        0,
                        ',',
                        '.'
                    ),


                'sisa_tagihan' =>
                    $sisaTagihan,

                'sisa_tagihan_format' =>
                    number_format(
                        $sisaTagihan,
                        0,
                        ',',
                        '.'
                    ),


                'denda' =>
                    $dendaFinal,

                'denda_format' =>
                    number_format(
                        $dendaFinal,
                        0,
                        ',',
                        '.'
                    ),


                'admin_keterlambatan' =>
                    $adminKeterlambatan,

                'admin_keterlambatan_format' =>
                    number_format(
                        $adminKeterlambatan,
                        0,
                        ',',
                        '.'
                    ),


                'total_tambahan' =>
                    $totalTambahan,

                'total_tambahan_format' =>
                    number_format(
                        $totalTambahan,
                        0,
                        ',',
                        '.'
                    ),


                'total_dibayar' =>
                    $jumlahBayar,

                'total_dibayar_format' =>
                    number_format(
                        $jumlahBayar,
                        0,
                        ',',
                        '.'
                    ),
            ],


            'summary' => [

                'total_angsuran' =>
                    $totalAngsuran,

                'sudah_dibayar' =>
                    $sudahDibayar,

                'sisa_angsuran' =>
                    $sisaAngsuran,

                'outstanding' =>
                    $outstanding,

                'outstanding_format' =>
                    number_format(
                        $outstanding,
                        0,
                        ',',
                        '.'
                    ),
            ],
        ];
    });
    }


    /*
    |--------------------------------------------------------------------------
    | NOMOR PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    protected function generateNomor(): string
    {
        $tahun =
            date('Y');

        $last =
            PembayaranAngsuran::whereYear(
                'created_at',
                $tahun
            )
            ->lockForUpdate()
            ->count()
            + 1;


        return sprintf(
            'BYR/%s/%05d',
            $tahun,
            $last
        );
    }
}