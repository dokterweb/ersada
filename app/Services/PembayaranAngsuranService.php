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
        $angsuran->loadMissing(['pembiayaan.akad.pencairan']);

        $tanggalBayar = $tanggalBayar
            ? Carbon::parse($tanggalBayar)->startOfDay()
            : Carbon::today();

        $tanggalJatuhTempo = Carbon::parse(
            $angsuran->tanggal_jatuh_tempo
        )->startOfDay();

        $pembiayaan = $angsuran->pembiayaan;

        /*
        |--------------------------------------------------------------------------
        | 1. Konfigurasi denda berdasarkan tenor
        |--------------------------------------------------------------------------
        */

        $jenisTenor = $pembiayaan->jenis_tenor;

        // Tenor pendek: 0,20% per hari.
        // Tenor panjang: 0,10% per hari.
        $persenDendaHarian = $jenisTenor === 'pendek'
            ? 0.20
            : 0.10;

        $dendaPerHari = (int) round(
            (float) $pembiayaan->plafond
            * ($persenDendaHarian / 100)
        );

        // Admin hanya berlaku untuk tenor panjang.
        $tarifAdmin = $jenisTenor === 'panjang'
            ? 15000
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 2. Periksa apakah angsuran sudah jatuh tempo
        |--------------------------------------------------------------------------
        */

        $tglTelatBayar = optional(
            $pembiayaan->akad?->pencairan
        )->tgl_telat_bayar;

        $tglTelatBayar = $tglTelatBayar ?: 10;

        $tanggalMulaiDenda = $tanggalJatuhTempo->copy();

        if ($tanggalBayar->lte($tanggalJatuhTempo)) {
            return [
                'hari_terlambat' => 0,
                'persen_denda_harian' => $persenDendaHarian,
                'denda_per_hari' => $dendaPerHari,
                'denda_berjalan' => 0,
                'denda_sudah_dibayar' => 0,
                'diskon_denda' => 0,
                'diskon_pengajuan' => 0,
                'denda_tersisa' => 0,
                'admin_berjalan' => 0,
                'admin_sudah_dibayar' => 0,
                'admin_tersisa' => 0,
                'total_tambahan' => 0,
                'tgl_telat_bayar' => $tglTelatBayar,
                'tanggal_mulai_denda' => $tanggalMulaiDenda
                    ->format('Y-m-d'),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Hitung hari keterlambatan
        |--------------------------------------------------------------------------
        |
        | Jika masih dalam bulan dan tahun jatuh tempo:
        | hitung jumlah hari keterlambatan sebenarnya.
        |
        | Jika sudah memasuki bulan kalender berikutnya:
        | gunakan maksimal 30 hari.
        */

        $bulanJatuhTempo = $tanggalJatuhTempo->format('Y-m');
        $bulanPerhitungan = $tanggalBayar->format('Y-m');

    /*
        |--------------------------------------------------------------------------
        | CEK SUDAH MEMASUKI BULAN KALENDER BERIKUTNYA
        |--------------------------------------------------------------------------
        */

        $tanggalBatasBulan = $tanggalJatuhTempo->copy()
            ->startOfMonth()
            ->addMonth();

        $sudahLewatBulan = $tanggalBayar->gte($tanggalBatasBulan);

        if ($sudahLewatBulan) {
            $hariTerlambat = 30;
        } else {
            $hariTerlambat = (int) $tanggalJatuhTempo
                ->diffInDays($tanggalBayar);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Hitung denda berjalan
        |--------------------------------------------------------------------------
        */

        $dendaBerjalan = $dendaPerHari * $hariTerlambat;

        /*
        |--------------------------------------------------------------------------
        | 5. Ambil histori pembayaran angsuran
        |--------------------------------------------------------------------------
        */

        $histori = PembayaranAngsuran::where(
            'angsuran_id',
            $angsuran->id
        )->get();

        $dendaSudahDibayar = (int) $histori->sum('denda');

        $diskonDirealisasikan = (int) $histori->sum('diskon_denda');

        $adminSudahDibayar = (int) $histori->sum(
            'admin_keterlambatan'
        );

        /*
        |--------------------------------------------------------------------------
        | 6. Ambil pengajuan diskon yang disetujui dan belum digunakan
        |--------------------------------------------------------------------------
        */

        $pengajuanDiskon = PengajuanDiskonDenda::where(
            'angsuran_id',
            $angsuran->id
        )
            ->where('status', 'disetujui')
            ->whereNull('pembayaran_id')
            ->whereNull('digunakan_at')
            ->orderByDesc('disetujui_at')
            ->orderByDesc('id')
            ->first();

        $diskonPengajuan = $pengajuanDiskon
            ? (int) $pengajuanDiskon->diskon_denda
            : 0;

        $totalDiskon = $diskonDirealisasikan + $diskonPengajuan;

        /*
        |--------------------------------------------------------------------------
        | 7. Hitung sisa denda
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
        | 8. Hitung admin keterlambatan
        |--------------------------------------------------------------------------
        |
        | Tenor panjang:
        | - Rp15.000 per angsuran setelah memasuki bulan berikutnya.
        | - Tidak berlipat ganda walaupun keterlambatan berlangsung
        |   lebih dari satu bulan.
        |
        | Tenor pendek:
        | - Tidak dikenakan admin keterlambatan.
        */

        $adminBerjalan = (
            $jenisTenor === 'panjang' && $sudahLewatBulan
        )
            ? $tarifAdmin
            : 0;

        $adminTersisa = max(
            0,
            $adminBerjalan - $adminSudahDibayar
        );

        /*
        |--------------------------------------------------------------------------
        | 9. Total tambahan yang perlu dibayar
        |--------------------------------------------------------------------------
        */

        $totalTambahan = $dendaTersisa + $adminTersisa;


        return [
            'hari_terlambat' => $hariTerlambat,
            'persen_denda_harian' => $persenDendaHarian,
            'denda_per_hari' => $dendaPerHari,

            'denda_berjalan' => $dendaBerjalan,
            'denda_berjalan_format' => number_format(
                $dendaBerjalan,
                0,
                ',',
                '.'
            ),

            'denda_sudah_dibayar' => $dendaSudahDibayar,
            'denda_sudah_dibayar_format' => number_format(
                $dendaSudahDibayar,
                0,
                ',',
                '.'
            ),

            'diskon_denda' => $totalDiskon,
            'diskon_denda_format' => number_format(
                $totalDiskon,
                0,
                ',',
                '.'
            ),

            'diskon_pengajuan' => $diskonPengajuan,

            'denda_tersisa' => $dendaTersisa,
            'denda_tersisa_format' => number_format(
                $dendaTersisa,
                0,
                ',',
                '.'
            ),

            'admin_berjalan' => $adminBerjalan,
            'admin_berjalan_format' => number_format(
                $adminBerjalan,
                0,
                ',',
                '.'
            ),

            'admin_sudah_dibayar' => $adminSudahDibayar,
            'admin_sudah_dibayar_format' => number_format(
                $adminSudahDibayar,
                0,
                ',',
                '.'
            ),

            'admin_tersisa' => $adminTersisa,
            'admin_tersisa_format' => number_format(
                $adminTersisa,
                0,
                ',',
                '.'
            ),

            'total_tambahan' => $totalTambahan,
            'total_tambahan_format' => number_format(
                $totalTambahan,
                0,
                ',',
                '.'
            ),

            'tgl_telat_bayar' => $tglTelatBayar,
            'tanggal_mulai_denda' => $tanggalMulaiDenda->format('Y-m-d'),
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
        $prefix = 'BYR/' . $tahun . '/';

        $last = PembayaranAngsuran::where(
            'nomor_pembayaran',
            'like',
            $prefix . '%'
        )
            ->lockForUpdate()
            ->orderByDesc('nomor_pembayaran')
            ->value('nomor_pembayaran');

        $nomorTerakhir = $last
            ? (int) substr($last, strlen($prefix))
            : 0;

        $nomorBerikutnya = $nomorTerakhir + 1;

        return sprintf(
            'BYR/%s/%05d',
            $tahun,
            $nomorBerikutnya
        );
    }

}