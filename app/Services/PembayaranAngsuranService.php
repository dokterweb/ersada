<?php

namespace App\Services;

use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanDiskonDenda;

class PembayaranAngsuranService
{
    protected AuditTrailService $auditTrail;

    public function __construct(AuditTrailService $auditTrail)
    {
        $this->auditTrail = $auditTrail;
    }

    /**
     * ============================================================
     * PREVIEW DENDA
     * ============================================================
     *
     * Menghitung kewajiban keterlambatan sampai tanggal tertentu.
     *
     * Rumus:
     *
     * tanggal jatuh tempo
     *        ↓
     * tgl_telat_bayar
     *        ↓
     * hari terlambat
     *        ↓
     * denda berjalan
     *        ↓
     * dikurangi denda yang sudah dibayar
     *        ↓
     * denda tersisa
     *
     * Admin:
     * setiap 30 hari = Rp15.000
     */
    
    public function previewDenda(
    Angsuran $angsuran,
    ?string $tanggalBayar = null
): array {

    /*
    |--------------------------------------------------------------------------
    | LOAD DATA
    |--------------------------------------------------------------------------
    */

    $angsuran->loadMissing([
        'pembiayaan.akad.pencairan',
    ]);

    $pembiayaan = $angsuran->pembiayaan;


    /*
    |--------------------------------------------------------------------------
    | TANGGAL BAYAR
    |--------------------------------------------------------------------------
    */

    $tanggalBayar = $tanggalBayar
        ? Carbon::parse($tanggalBayar)->startOfDay()
        : Carbon::today();


    /*
    |--------------------------------------------------------------------------
    | TANGGAL JATUH TEMPO
    |--------------------------------------------------------------------------
    */

    $tanggalJatuhTempo = Carbon::parse(
        $angsuran->tanggal_jatuh_tempo
    )->startOfDay();


    /*
    |--------------------------------------------------------------------------
    | AMBIL TGL TELAT BAYAR
    |--------------------------------------------------------------------------
    */

    $tglTelatBayar = optional(
        $pembiayaan->akad?->pencairan
    )->tgl_telat_bayar;


    /*
    | Default
    */

    $tglTelatBayar = $tglTelatBayar ?: 10;


   /*
    |--------------------------------------------------------------------------
    | TANGGAL BATAS BEBAS DENDA
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | Jatuh tempo  : 01-08
    | Toleransi    : 10 hari
    |
    | 10-08 = masih bebas denda
    | 11-08 = mulai denda, dihitung 1 hari
    |
    */

    $tanggalMulaiDenda = $tanggalJatuhTempo
        ->copy()
        ->addDays($tglTelatBayar);


    /*
    |--------------------------------------------------------------------------
    | BELUM MASUK PERIODE DENDA
    |--------------------------------------------------------------------------
    */

    if ($tanggalBayar->lte($tanggalMulaiDenda)) {

        return [

            'hari_terlambat' => 0,

            'persen_denda_harian' =>
                $pembiayaan->jenis_tenor === 'pendek'
                    ? 0.20
                    : 0.10,

            'denda_per_hari' => 0,

            'denda_berjalan' => 0,
            'denda_berjalan_format' => '0',

            'denda_sudah_dibayar' => 0,
            'denda_sudah_dibayar_format' => '0',

            'diskon_denda' => 0,
            'diskon_denda_format' => '0',

            'diskon_direalisasikan' => 0,
            'diskon_direalisasikan_format' => '0',

            'diskon_pengajuan' => 0,
            'diskon_pengajuan_format' => '0',

            'pengajuan_diskon_id' => null,
            'status_diskon' => 'tidak_ada',

            'denda_tersisa' => 0,
            'denda_tersisa_format' => '0',

            'admin_berjalan' => 0,
            'admin_berjalan_format' => '0',

            'admin_sudah_dibayar' => 0,
            'admin_sudah_dibayar_format' => '0',

            'admin_tersisa' => 0,
            'admin_tersisa_format' => '0',

            'total_tambahan' => 0,
            'total_tambahan_format' => '0',

            'tgl_telat_bayar' => $tglTelatBayar,

            'tanggal_mulai_denda' =>
                $tanggalMulaiDenda->format('Y-m-d'),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG HARI TERLAMBAT
    |--------------------------------------------------------------------------
    */

    $hariTerlambat = $tanggalMulaiDenda->diffInDays(
        $tanggalBayar
    );


    /*
    |--------------------------------------------------------------------------
    | PERSENTASE DENDA
    |--------------------------------------------------------------------------
    */

    $persenDendaHarian =
        $pembiayaan->jenis_tenor === 'pendek'
            ? 0.20
            : 0.10;


    /*
    |--------------------------------------------------------------------------
    | DENDA PER HARI
    |--------------------------------------------------------------------------
    */

    $dendaPerHari = round(
        $pembiayaan->plafond *
        ($persenDendaHarian / 100)
    );


    /*
    |--------------------------------------------------------------------------
    | TOTAL DENDA BERJALAN
    |--------------------------------------------------------------------------
    */

    $dendaBerjalan =
        $dendaPerHari * $hariTerlambat;


    /*
    |--------------------------------------------------------------------------
    | HISTORI PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    $histori = PembayaranAngsuran::where(
        'angsuran_id',
        $angsuran->id
    )->get();


    /*
    |--------------------------------------------------------------------------
    | DENDA YANG SUDAH DIBAYAR
    |--------------------------------------------------------------------------
    */

    $dendaSudahDibayar =
        $histori->sum('denda');


    /*
    |--------------------------------------------------------------------------
    | DISKON YANG SUDAH DIREALISASIKAN
    |--------------------------------------------------------------------------
    |
    | Ini penting.
    |
    | Jika pembayaran sebelumnya sudah menggunakan diskon,
    | maka diskon tersebut tetap harus mengurangi kewajiban
    | meskipun pengajuannya sudah memiliki pembayaran_id.
    |
    */

    $diskonDirealisasikan =
        $histori->sum('diskon_denda');


    /*
    |--------------------------------------------------------------------------
    | PENGAJUAN DISKON YANG SUDAH DISETUJUI
    |--------------------------------------------------------------------------
    |
    | Hanya pengajuan:
    |
    | status       = disetujui
    | pembayaran_id = NULL
    | digunakan_at  = NULL
    |
    | yang masih boleh digunakan.
    |
    */

    $pengajuanDiskon = PengajuanDiskonDenda::where(
        'angsuran_id',
        $angsuran->id
    )
    ->where('status', 'disetujui')
    ->whereNull('pembayaran_id')
    ->whereNull('digunakan_at')
    ->latest('disetujui_at')
    ->latest('id')
    ->first();


    /*
    |--------------------------------------------------------------------------
    | DISKON YANG BELUM DIGUNAKAN
    |--------------------------------------------------------------------------
    */

    $diskonPengajuan = $pengajuanDiskon
        ? (int) $pengajuanDiskon->diskon_denda
        : 0;


    /*
    |--------------------------------------------------------------------------
    | TOTAL DISKON YANG MENGURANGI KEWAJIBAN
    |--------------------------------------------------------------------------
    */

    $totalDiskon =
        $diskonDirealisasikan
        + $diskonPengajuan;


    /*
    |--------------------------------------------------------------------------
    | DENDA TERSISA
    |--------------------------------------------------------------------------
    */

    $dendaTersisa = max(
        0,
        $dendaBerjalan
        - $dendaSudahDibayar
        - $totalDiskon
    );


    /*
    |--------------------------------------------------------------------------
    | ADMIN KETERLAMBATAN
    |--------------------------------------------------------------------------
    |
    | Setiap 30 hari = Rp15.000
    |--------------------------------------------------------------------------
    */

    $jumlahAdmin =
        intdiv($hariTerlambat, 30);


    $adminBerjalan =
        $jumlahAdmin * 15000;


    /*
    |--------------------------------------------------------------------------
    | ADMIN SUDAH DIBAYAR
    |--------------------------------------------------------------------------
    */

    $adminSudahDibayar =
        $histori->sum('admin_keterlambatan');


    /*
    |--------------------------------------------------------------------------
    | ADMIN TERSISA
    |--------------------------------------------------------------------------
    */

    $adminTersisa = max(
        0,
        $adminBerjalan
        - $adminSudahDibayar
    );


    /*
    |--------------------------------------------------------------------------
    | TOTAL TAMBAHAN
    |--------------------------------------------------------------------------
    */

    $totalTambahan =
        $dendaTersisa
        + $adminTersisa;


    /*
    |--------------------------------------------------------------------------
    | RETURN
    |--------------------------------------------------------------------------
    */

    return [

        'hari_terlambat' =>
            $hariTerlambat,

        'persen_denda_harian' =>
            $persenDendaHarian,

        'denda_per_hari' =>
            $dendaPerHari,


        /*
        | Denda
        */

        'denda_berjalan' =>
            $dendaBerjalan,

        'denda_berjalan_format' =>
            number_format(
                $dendaBerjalan,
                0,
                ',',
                '.'
            ),


        /*
        | Denda sudah dibayar
        */

        'denda_sudah_dibayar' =>
            $dendaSudahDibayar,

        'denda_sudah_dibayar_format' =>
            number_format(
                $dendaSudahDibayar,
                0,
                ',',
                '.'
            ),


        /*
        | Diskon aktif dari approval
        */

        'diskon_denda' =>
            $diskonPengajuan,

        'diskon_denda_format' =>
            number_format(
                $diskonPengajuan,
                0,
                ',',
                '.'
            ),


        /*
        | Diskon yang sudah direalisasikan
        */

        'diskon_direalisasikan' =>
            $diskonDirealisasikan,

        'diskon_direalisasikan_format' =>
            number_format(
                $diskonDirealisasikan,
                0,
                ',',
                '.'
            ),


        /*
        | Diskon pengajuan
        */

        'diskon_pengajuan' =>
            $diskonPengajuan,

        'diskon_pengajuan_format' =>
            number_format(
                $diskonPengajuan,
                0,
                ',',
                '.'
            ),


        /*
        | Informasi pengajuan
        */

        'pengajuan_diskon_id' =>
            $pengajuanDiskon?->id,

        'status_diskon' =>
            $pengajuanDiskon
                ? 'disetujui'
                : (
                    $diskonDirealisasikan > 0
                        ? 'disetujui'
                        : 'tidak_ada'
                ),


        /*
        | Denda tersisa
        */

        'denda_tersisa' =>
            $dendaTersisa,

        'denda_tersisa_format' =>
            number_format(
                $dendaTersisa,
                0,
                ',',
                '.'
            ),


        /*
        | Admin
        */

        'admin_berjalan' =>
            $adminBerjalan,

        'admin_berjalan_format' =>
            number_format(
                $adminBerjalan,
                0,
                ',',
                '.'
            ),

        'admin_sudah_dibayar' =>
            $adminSudahDibayar,

        'admin_sudah_dibayar_format' =>
            number_format(
                $adminSudahDibayar,
                0,
                ',',
                '.'
            ),

        'admin_tersisa' =>
            $adminTersisa,

        'admin_tersisa_format' =>
            number_format(
                $adminTersisa,
                0,
                ',',
                '.'
            ),


        /*
        | Total
        */

        'total_tambahan' =>
            $totalTambahan,

        'total_tambahan_format' =>
            number_format(
                $totalTambahan,
                0,
                ',',
                '.'
            ),


        /*
        | Parameter denda
        */

        'tgl_telat_bayar' =>
            $tglTelatBayar,

        'tanggal_mulai_denda' =>
            $tanggalMulaiDenda->format('Y-m-d'),
    ];
}


    /**
     * ============================================================
     * BAYAR ANGSURAN
     * ============================================================
     *
     * Urutan alokasi:
     *
     * 1. Angsuran
     * 2. Denda
     * 3. Admin
     *
     * Tidak ada saldo denda/admin di tabel angsurans.
     *
     * Semua histori tetap tersimpan di pembayaran_angsurans.
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
        | RELOAD DATA
        |--------------------------------------------------------------------------
        */

        $angsuran = Angsuran::with([
            'pembiayaan.akad.pencairan',
            'pembiayaan.pengajuan.nasabah',
        ])
        ->lockForUpdate()
        ->findOrFail($angsuran->id);


        /*
        |--------------------------------------------------------------------------
        | VALIDASI STATUS
        |--------------------------------------------------------------------------
        */

        if ($angsuran->status === 'dibayar') {

            throw new \Exception(
                'Angsuran ini sudah lunas.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NOMINAL UANG MASUK
        |--------------------------------------------------------------------------
        */

        $uangMasuk = (int) $data['jumlah_bayar'];

        if ($uangMasuk <= 0) {

            throw new \Exception(
                'Nominal pembayaran harus lebih besar dari nol.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TANGGAL PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $tanggalBayar = Carbon::parse(
            $data['tanggal_bayar']
        )->startOfDay();


        /*
        |--------------------------------------------------------------------------
        | KUNCI PENGAJUAN DISKON
        |--------------------------------------------------------------------------
        |
        | Lock pengajuan agar tidak bisa digunakan oleh dua transaksi
        | secara bersamaan.
        |
        */

        $pengajuanDiskon = PengajuanDiskonDenda::where(
            'angsuran_id',
            $angsuran->id
        )
        ->where('status', 'disetujui')
        ->whereNull('pembayaran_id')
        ->whereNull('digunakan_at')
        ->latest('disetujui_at')
        ->latest('id')
        ->lockForUpdate()
        ->first();


        /*
        |--------------------------------------------------------------------------
        | PREVIEW DENDA TERBARU
        |--------------------------------------------------------------------------
        */

        $preview = $this->previewDenda(
            $angsuran,
            $tanggalBayar->format('Y-m-d')
        );


        /*
        |--------------------------------------------------------------------------
        | TOTAL ANGSURAN YANG MASIH HARUS DIBAYAR
        |--------------------------------------------------------------------------
        */

        $sisaAngsuran = max(
            0,
            $angsuran->total_angsuran
            - $angsuran->total_terbayar
        );


        /*
        |--------------------------------------------------------------------------
        | DISKON DARI APPROVAL
        |--------------------------------------------------------------------------
        |
        | Jangan mengambil diskon dari request Kasir.
        |
        */

        $diskonDenda = (int) $preview['diskon_denda'];

        $statusDiskon = $diskonDenda > 0
            ? 'disetujui'
            : 'tidak_ada';


        /*
        |--------------------------------------------------------------------------
        | DENDA FINAL
        |--------------------------------------------------------------------------
        |
        | previewDenda() sudah mengurangi:
        |
        | denda berjalan
        | - denda sudah dibayar
        | - diskon yang sudah direalisasikan
        | - diskon approval yang belum digunakan
        |
        */

        $dendaTersisa = (int) $preview['denda_tersisa'];


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        $adminTersisa =
            (int) $preview['admin_tersisa'];


        /*
        |--------------------------------------------------------------------------
        | MULAI ALOKASI UANG
        |--------------------------------------------------------------------------
        */

        $sisaUang = $uangMasuk;


        /*
        |--------------------------------------------------------------------------
        | 1. BAYAR ANGSURAN TERLEBIH DAHULU
        |--------------------------------------------------------------------------
        */

        $bayarAngsuran = min(
            $sisaUang,
            $sisaAngsuran
        );

        $sisaUang -= $bayarAngsuran;


        /*
        |--------------------------------------------------------------------------
        | 2. BAYAR DENDA
        |--------------------------------------------------------------------------
        */

        $bayarDenda = min(
            $sisaUang,
            $dendaTersisa
        );

        $sisaUang -= $bayarDenda;


        /*
        |--------------------------------------------------------------------------
        | 3. BAYAR ADMIN
        |--------------------------------------------------------------------------
        */

        $bayarAdmin = min(
            $sisaUang,
            $adminTersisa
        );

        $sisaUang -= $bayarAdmin;


        /*
        |--------------------------------------------------------------------------
        | JIKA ADA KELEBIHAN PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        if ($sisaUang > 0) {

            throw new \Exception(
                'Nominal pembayaran melebihi seluruh kewajiban.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMBAYARAN ANGSURAN TERBARU
        |--------------------------------------------------------------------------
        */

        $totalTerbayarAngsuran =
            $angsuran->total_terbayar
            + $bayarAngsuran;


        /*
        |--------------------------------------------------------------------------
        | SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            0,
            $angsuran->total_angsuran
            - $totalTerbayarAngsuran
        );


        /*
        |--------------------------------------------------------------------------
        | STATUS ANGSURAN
        |--------------------------------------------------------------------------
        */

        if ($sisaTagihan == 0) {

            $status = 'dibayar';

            $tanggalLunas =
                $tanggalBayar->format('Y-m-d');

        } else {

            $tanggalLunas = null;

            if (
                $tanggalBayar->gte(
                    Carbon::parse(
                        $angsuran->tanggal_jatuh_tempo
                    )->startOfDay()
                )
            ) {

                $status = 'jatuh_tempo';

            } else {

                $status = 'belum_jatuh_tempo';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAMBAHAN
        |--------------------------------------------------------------------------
        */

        $totalTambahan =
            $bayarDenda
            + $bayarAdmin;


        /*
        |--------------------------------------------------------------------------
        | TOTAL UANG YANG MASUK
        |--------------------------------------------------------------------------
        */

        $totalDibayar =
            $bayarAngsuran
            + $bayarDenda
            + $bayarAdmin;


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $pembayaran = PembayaranAngsuran::create([

            'angsuran_id' =>
                $angsuran->id,

            'nomor_pembayaran' =>
                $this->generateNomor(),

            'tanggal_bayar' =>
                $tanggalBayar->format('Y-m-d'),


            /*
            | Uang yang masuk ke pokok + bunga
            */

            'jumlah_bayar' =>
                $bayarAngsuran,


            /*
            | Denda yang benar-benar dibayar
            */

            'denda' =>
                $bayarDenda,


            /*
            | Diskon yang digunakan
            */

            'diskon_denda' =>
                $diskonDenda,


            /*
            | Admin yang dibayar
            */

            'admin_keterlambatan' =>
                $bayarAdmin,


            /*
            | Total uang masuk
            */

            'total_dibayar' =>
                $totalDibayar,


            /*
            | Status diskon
            */

            'status_diskon' =>
                $statusDiskon,


            /*
            | Snapshot approval
            */

            'diskon_disetujui_oleh' =>
                $pengajuanDiskon?->disetujui_oleh,

            'diskon_disetujui_at' =>
                $pengajuanDiskon?->disetujui_at,

            'alasan_diskon' =>
                $pengajuanDiskon?->alasan,


            /*
            | Pembayaran
            */

            'metode' =>
                $data['metode'] ?? 'tunai',

            'keterangan' =>
                $data['keterangan'] ?? null,

            'created_by' =>
                Auth::id(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | TANDAI PENGAJUAN DISKON SUDAH DIGUNAKAN
        |--------------------------------------------------------------------------
        |
        | Diskon hanya direalisasikan jika memang ada diskon approval.
        |
        */

        if (
            $pengajuanDiskon &&
            $diskonDenda > 0
        ) {

            $pengajuanDiskon->update([

                'pembayaran_id' =>
                    $pembayaran->id,

                'digunakan_at' =>
                    now(),
            ]);
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

            'total_terbayar' =>
                $totalTerbayarAngsuran,

            'sisa_tagihan' =>
                $sisaTagihan,
        ]);


        /*
        |--------------------------------------------------------------------------
        | SUMMARY PEMBIAYAAN
        |--------------------------------------------------------------------------
        */

        $angsurans =
            $angsuran->pembiayaan
                ->angsurans()
                ->orderBy('angsuran_ke')
                ->get();


        $totalAngsuran =
            $angsurans->count();


        $sudahDibayar =
            $angsurans
                ->where('status', 'dibayar')
                ->count();


        $sisaAngsuranCount =
            $totalAngsuran
            - $sudahDibayar;


        $outstanding =
            $angsurans
                ->where('status', '!=', 'dibayar')
                ->sortBy('angsuran_ke')
                ->first()?->sisa_pokok ?? 0;


        /*
        |--------------------------------------------------------------------------
        | AUDIT TRAIL
        |--------------------------------------------------------------------------
        */

        $auditDiskon = $diskonDenda > 0
            ? ' | Diskon : Rp ' .
                number_format(
                    $diskonDenda,
                    0,
                    ',',
                    '.'
                )
            : '';


        $this->auditTrail->log(
            $angsuran->pembiayaan,
            'Angsuran',
            'Pembayaran Angsuran',
            'Angsuran Ke-' .
            $angsuran->angsuran_ke .
            ' | Angsuran : Rp ' .
            number_format(
                $bayarAngsuran,
                0,
                ',',
                '.'
            ) .
            ' | Denda : Rp ' .
            number_format(
                $bayarDenda,
                0,
                ',',
                '.'
            ) .
            $auditDiskon .
            ' | Admin : Rp ' .
            number_format(
                $bayarAdmin,
                0,
                ',',
                '.'
            ) .
            ' | Total : Rp ' .
            number_format(
                $totalDibayar,
                0,
                ',',
                '.'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return [

            'pembayaran' =>
                $pembayaran,

            'preview' =>
                $preview,

            'alokasi' => [

                'uang_masuk' =>
                    $uangMasuk,

                'bayar_angsuran' =>
                    $bayarAngsuran,

                'bayar_denda' =>
                    $bayarDenda,

                'bayar_admin' =>
                    $bayarAdmin,

                'diskon_denda' =>
                    $diskonDenda,

                'total_dibayar' =>
                    $totalDibayar,
            ],


            'row' => [

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
                    $totalTerbayarAngsuran,

                'total_terbayar_format' =>
                    number_format(
                        $totalTerbayarAngsuran,
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
                    $bayarDenda,

                'denda_format' =>
                    number_format(
                        $bayarDenda,
                        0,
                        ',',
                        '.'
                    ),


                'diskon_denda' =>
                    $diskonDenda,

                'diskon_denda_format' =>
                    number_format(
                        $diskonDenda,
                        0,
                        ',',
                        '.'
                    ),


                'admin_keterlambatan' =>
                    $bayarAdmin,

                'admin_keterlambatan_format' =>
                    number_format(
                        $bayarAdmin,
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
                    $totalDibayar,

                'total_dibayar_format' =>
                    number_format(
                        $totalDibayar,
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
                    $sisaAngsuranCount,

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


    /**
     * ============================================================
     * GENERATE NOMOR PEMBAYARAN
     * ============================================================
     */
    protected function generateNomor(): string
    {
        $tahun = date('Y');

        $last = PembayaranAngsuran::whereYear(
            'created_at',
            $tahun
        )
        ->lockForUpdate()
        ->count() + 1;

        return sprintf(
            'BYR/%s/%05d',
            $tahun,
            $last
        );
    }
}