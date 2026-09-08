<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Services\AngsuranService;
use App\Services\DendaService;
use App\Services\PdfService;
use App\Services\PembayaranAngsuranService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AngsuranController extends Controller
{
     public function __construct(
        protected PembayaranAngsuranService $service,
        protected AngsuranService $angsuranService,
        protected DendaService $dendaService
    ) {}

    public function index(Request $request)
    {
        $query = Pembiayaan::with(['pengajuan.nasabah','pengajuan.marketing', 'pengajuan.cabang'])
        ->where('status', 'dicairkan');

        // Pencarian
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_pembiayaan', 'like', "%{$keyword}%")
                ->orWhereHas('pengajuan.nasabah', function ($q2) use ($keyword) {
                    $q2->where('nama', 'like', "%{$keyword}%");
                });
            });
        }

        $pembiayaans = $query->latest()->paginate(15);

        return view('angsuran.index', compact('pembiayaans'));
    }

    /**
     * Detail Jadwal Angsuran
     */
    public function show(Pembiayaan $pembiayaan)
    {
        $this->angsuranService->syncStatus(
            $pembiayaan->id
        );

        $pembiayaan->load([
            'pengajuan.nasabah',
            'pengajuan.marketing',
            'pengajuan.cabang',
            'akad.pencairan',
            'angsurans' => function ($q) {
                $q->with('pelunasan')
                ->orderBy('angsuran_ke');
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | PREVIEW DENDA
        |--------------------------------------------------------------------------
        */

        foreach ($pembiayaan->angsurans as $item) {

            $item->preview_denda =
                $this->service->previewDenda($item);

        }
       /*  dd($pembiayaan->angsurans->map(function ($item) {
            return [
                'id' => $item->id,
                'angsuran_ke' => $item->angsuran_ke,
                'status' => $item->status,
                'tanggal_jatuh_tempo' => $item->tanggal_jatuh_tempo,
                'preview_denda' => $item->preview_denda,
            ];
        })); */

        $angsurans = $pembiayaan->angsurans;

        $totalAngsuran = $angsurans->count();

        $sudahDibayar = $angsurans
            ->where('status', 'dibayar')
            ->count();

        $sisaAngsuran =
            $totalAngsuran - $sudahDibayar;

        $outstanding = $angsurans
            ->where('status', '!=', 'dibayar')
            ->sum('sisa_pokok');

        return view(
            'angsuran.show',
            compact(
                'pembiayaan',
                'totalAngsuran',
                'sudahDibayar',
                'sisaAngsuran',
                'outstanding'
            )
        );
    }

    /**
     * Form Pembayaran
     */
    public function create(Angsuran $angsuran)
    {
        $angsuran->load(['pembiayaan.pengajuan.nasabah']);

        return view('angsuran.create', compact('angsuran'));
    }

   
    public function store(
        Request $request,
        Angsuran $angsuran
    ) {

        $request->validate([

            'tanggal_bayar' =>
                'required|date',

            'jumlah_bayar' =>
                'required|numeric|min:1',

            'metode' =>
                'required|in:tunai,transfer',

            'keterangan' =>
                'nullable|string',

            'diskon' =>
                'nullable|numeric|min:0',
        ]);


        try {

            $result =
                $this->service->bayar(
                    $angsuran,
                    $request->all()
                );


            return response()->json([

                'success' =>
                    true,

                'message' =>
                    'Pembayaran berhasil.',

                'nominal' =>
                    number_format(
                        $request->jumlah_bayar,
                        0,
                        ',',
                        '.'
                    ),

                'row' =>
                    $result['row'],

                'summary' =>
                    $result['summary'],
            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' =>
                    false,

                'message' =>
                    $e->getMessage(),

            ], 422);
        }
    }

    public function history(Angsuran $angsuran)
    {
        $angsuran->load([
            'pembiayaan.pengajuan.nasabah',
            'pembayaranAngsurans.creator'
        ]);

        return view('angsuran.history', compact('angsuran'));
    }

    /**
     * Detail Pembayaran
     */
    public function detail(PembayaranAngsuran $pembayaran)
    {
        $pembayaran->load([
            'angsuran.pembiayaan.pengajuan.nasabah',
            'creator'
        ]);

        return view('angsuran.detail', compact('pembayaran'));
    }

public function getAngsuran(Angsuran $angsuran)
{
    $angsuran->load([
        'pembiayaan.pengajuan.nasabah',
        'pembiayaan.akad.pencairan',
    ]);

    /*
    |--------------------------------------------------------------------------
    | PREVIEW DENDA
    |--------------------------------------------------------------------------
    */

    $preview = $this->service->previewDenda(
        $angsuran,
        now()->format('Y-m-d')
    );


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,


        /*
        |--------------------------------------------------------------------------
        | DATA UTAMA
        |--------------------------------------------------------------------------
        */

        'id' => $angsuran->id,

        'nama' =>
            $angsuran->pembiayaan
                ->pengajuan
                ->nasabah
                ->nama,

        'nomor_pembiayaan' =>
            $angsuran->pembiayaan
                ->nomor_pembiayaan,

        'angsuran_ke' =>
            $angsuran->angsuran_ke,

        'tanggal_jatuh_tempo' =>
            $angsuran->tanggal_jatuh_tempo
                ->format('d-m-Y'),

        'status' =>
            ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $angsuran->status
                )
            ),


        /*
        |--------------------------------------------------------------------------
        | NILAI ANGSURAN
        |--------------------------------------------------------------------------
        */

        'pokok' =>
            number_format(
                $angsuran->pokok_angsuran,
                0,
                ',',
                '.'
            ),

        'bunga' =>
            number_format(
                $angsuran->bunga_angsuran,
                0,
                ',',
                '.'
            ),

        'total' =>
            number_format(
                $angsuran->total_angsuran,
                0,
                ',',
                '.'
            ),

        'total_angsuran' =>
            $angsuran->total_angsuran,


        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN ANGSURAN
        |--------------------------------------------------------------------------
        */

        'total_terbayar' =>
            $angsuran->total_terbayar,

        'total_terbayar_format' =>
            number_format(
                $angsuran->total_terbayar,
                0,
                ',',
                '.'
            ),

        'sisa_tagihan' =>
            $angsuran->sisa_tagihan,

        'sisa_tagihan_format' =>
            number_format(
                $angsuran->sisa_tagihan,
                0,
                ',',
                '.'
            ),


        /*
        |--------------------------------------------------------------------------
        | DENDA BERJALAN
        |--------------------------------------------------------------------------
        */

        'denda_berjalan' =>
            $preview['denda_berjalan'],

        'denda_berjalan_format' =>
            $preview['denda_berjalan_format'],


        /*
        |--------------------------------------------------------------------------
        | DISKON DENDA
        |--------------------------------------------------------------------------
        |
        | Hanya diskon yang sudah disetujui dan belum digunakan
        | yang dikirim sebagai diskon aktif.
        |
        */

        'diskon_denda' =>
            $preview['diskon_denda'],

        'diskon_denda_format' =>
            $preview['diskon_denda_format'],

        'pengajuan_diskon_id' =>
            $preview['pengajuan_diskon_id'],

        'status_diskon' =>
            $preview['status_diskon'],


        /*
        |--------------------------------------------------------------------------
        | DENDA SETELAH DISKON
        |--------------------------------------------------------------------------
        */

        'denda' =>
            $preview['denda_tersisa'],

        'denda_format' =>
            $preview['denda_tersisa_format'],


        /*
        |--------------------------------------------------------------------------
        | DENDA SUDAH DIBAYAR
        |--------------------------------------------------------------------------
        */

        'denda_sudah_dibayar' =>
            $preview['denda_sudah_dibayar'],

        'denda_sudah_dibayar_format' =>
            $preview['denda_sudah_dibayar_format'],


        /*
        |--------------------------------------------------------------------------
        | ADMIN KETERLAMBATAN
        |--------------------------------------------------------------------------
        */

        'admin_keterlambatan' =>
            $preview['admin_tersisa'],

        'admin_keterlambatan_format' =>
            $preview['admin_tersisa_format'],


        /*
        |--------------------------------------------------------------------------
        | HARI TERLAMBAT
        |--------------------------------------------------------------------------
        */

        'hari_terlambat' =>
            $preview['hari_terlambat'],

        'hari_terlambat_format' =>
            $preview['hari_terlambat'] . ' hari',


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAMBAHAN
        |--------------------------------------------------------------------------
        */

        'total_tambahan' =>
            $preview['total_tambahan'],

        'total_tambahan_format' =>
            $preview['total_tambahan_format'],


        /*
        |--------------------------------------------------------------------------
        | ADMIN DETAIL
        |--------------------------------------------------------------------------
        */

        'admin_berjalan' =>
            $preview['admin_berjalan'],

        'admin_berjalan_format' =>
            $preview['admin_berjalan_format'],

        'admin_sudah_dibayar' =>
            $preview['admin_sudah_dibayar'],

        'admin_sudah_dibayar_format' =>
            $preview['admin_sudah_dibayar_format'],


        /*
        |--------------------------------------------------------------------------
        | DEFAULT METODE
        |--------------------------------------------------------------------------
        */

        'metode' =>
            'tunai',


        /*
        |--------------------------------------------------------------------------
        | DEFAULT NOMINAL BAYAR
        |--------------------------------------------------------------------------
        */

        'default_bayar' =>
            $angsuran->sisa_tagihan
            + $preview['total_tambahan'],

        'default_bayar_format' =>
            number_format(
                $angsuran->sisa_tagihan
                + $preview['total_tambahan'],
                0,
                ',',
                '.'
            ),
    ]);
}


   public function previewDenda(Request $request,Angsuran $angsuran,  ?string $tanggalBayar = null) {
    $request->validate([
        'tanggal_bayar' => 'required|date',
    ]);

    $denda = $this->service->previewDenda(
        $angsuran,
        $request->tanggal_bayar
    );

    $sisaTagihan = (int) $angsuran->sisa_tagihan;

    $totalKewajiban =
        $sisaTagihan +
        $denda['total_tambahan'];

    return response()->json([

        'tanggal_bayar' =>
            $request->tanggal_bayar,

        'tanggal_batas_denda' =>
            $denda['tanggal_batas_denda'],

        'hari_terlambat' =>
            $denda['hari_terlambat'],

        'persen_denda' =>
            $denda['persen_denda'],

        'denda' =>
            $denda['denda'],

        'denda_format' =>
            $denda['denda_format'],

        'admin_keterlambatan' =>
            $denda['admin_keterlambatan'],

        'admin_keterlambatan_format' =>
            $denda['admin_format'],

        'total_tambahan' =>
            $denda['total_tambahan'],

        'total_tambahan_format' =>
            $denda['total_tambahan_format'],

        'sisa_tagihan' =>
            $sisaTagihan,

        'sisa_tagihan_format' =>
            number_format(
                $sisaTagihan,
                0,
                ',',
                '.'
            ),

        'total_kewajiban' =>
            $totalKewajiban,

        'total_kewajiban_format' =>
            number_format(
                $totalKewajiban,
                0,
                ',',
                '.'
            ),
    ]);
}



    public function cetakHistory(Angsuran $angsuran,PdfService $pdfService){
        // return $pdfService->history($angsuran)->stream('History-'.$angsuran->pembiayaan->nomor_pembiayaan.'.pdf');
        $filename = 'History-' .str_replace('/', '-', $angsuran->pembiayaan->nomor_pembiayaan) .'.pdf';
        return $pdfService->history($angsuran)->stream($filename);
    }

    public function getHistory(Angsuran $angsuran)
    {
        $angsuran->load([
            'pembiayaan.pengajuan.nasabah',
            'pembayaranAngsurans.creator',
        ]);

        /*
        |--------------------------------------------------------------------------
        | HISTORY PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $history = $angsuran->pembayaranAngsurans
            ->sortByDesc(function ($item) {
                return [
                    optional($item->tanggal_bayar)->timestamp ?? 0,
                    $item->id,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | FORMAT HISTORY
        |--------------------------------------------------------------------------
        */

        $historyData = $history->map(function ($item) {

            return [

                'id' =>
                    $item->id,

                'tanggal' =>
                    optional($item->tanggal_bayar)
                        ->format('d-m-Y'),

                'nomor' =>
                    $item->nomor_pembayaran,

                /*
                |--------------------------------------------------------------------------
                | ANGSURAN
                |--------------------------------------------------------------------------
                */

                'jumlah_bayar' =>
                    $item->jumlah_bayar,

                'jumlah_bayar_format' =>
                    number_format(
                        $item->jumlah_bayar,
                        0,
                        ',',
                        '.'
                    ),

                /*
                |--------------------------------------------------------------------------
                | DENDA
                |--------------------------------------------------------------------------
                */

                'denda' =>
                    $item->denda,

                'denda_format' =>
                    number_format(
                        $item->denda,
                        0,
                        ',',
                        '.'
                    ),

                /*
                |--------------------------------------------------------------------------
                | DISKON DENDA
                |--------------------------------------------------------------------------
                */

                'diskon_denda' =>
                    $item->diskon_denda,

                'diskon_denda_format' =>
                    number_format(
                        $item->diskon_denda,
                        0,
                        ',',
                        '.'
                    ),

                /*
                |--------------------------------------------------------------------------
                | STATUS DISKON
                |--------------------------------------------------------------------------
                */

                'status_diskon' =>
                    $item->status_diskon,

                /*
                |--------------------------------------------------------------------------
                | TOTAL PEMBAYARAN
                |--------------------------------------------------------------------------
                */

                'total' =>
                    $item->total_dibayar,

                'total_format' =>
                    number_format(
                        $item->total_dibayar,
                        0,
                        ',',
                        '.'
                    ),

                /*
                |--------------------------------------------------------------------------
                | METODE
                |--------------------------------------------------------------------------
                */

                'metode' =>
                    ucfirst($item->metode),

                /*
                |--------------------------------------------------------------------------
                | KASIR
                |--------------------------------------------------------------------------
                */

                'user' =>
                    $item->creator?->name ?? '-',
            ];
        });


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            /*
            |--------------------------------------------------------------------------
            | DATA ANGSURAN
            |--------------------------------------------------------------------------
            */

            'nama' =>
                $angsuran->pembiayaan
                    ->pengajuan
                    ->nasabah
                    ->nama,

            'nomor_pembiayaan' =>
                $angsuran->pembiayaan
                    ->nomor_pembiayaan,

            'angsuran_ke' =>
                $angsuran->angsuran_ke,

            'status' =>
                ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $angsuran->status
                    )
                ),

            /*
            |--------------------------------------------------------------------------
            | RINGKASAN
            |--------------------------------------------------------------------------
            */

            'total_tagihan' =>
                number_format(
                    $angsuran->total_angsuran,
                    0,
                    ',',
                    '.'
                ),

            'total_terbayar' =>
                number_format(
                    $angsuran->total_terbayar,
                    0,
                    ',',
                    '.'
                ),

            'sisa_tagihan' =>
                number_format(
                    $angsuran->sisa_tagihan,
                    0,
                    ',',
                    '.'
                ),

            /*
            |--------------------------------------------------------------------------
            | HISTORY
            |--------------------------------------------------------------------------
            */

            'history' =>
                $historyData->values(),
        ]);
    }

    private function syncStatusAngsuran(?int $pembiayaanId = null)
    {
        $query = Angsuran::query()
            ->where('status', '!=', 'dibayar')
            ->whereDate('tanggal_jatuh_tempo','<=',now()->toDateString());

        if ($pembiayaanId) {
            $query->where('pembiayaan_id',$pembiayaanId);
        }

        $query->update(['status' => 'jatuh_tempo',]);
    }
}
