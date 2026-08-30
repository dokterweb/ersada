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
                $q->orderBy('angsuran_ke');
            },
        ]);

        $angsurans = $pembiayaan->angsurans;
        $totalAngsuran = $angsurans->count();
        $sudahDibayar = $angsurans->where('status', 'dibayar')->count();
        $sisaAngsuran = $totalAngsuran - $sudahDibayar;
        $outstanding = $angsurans->where('status', '!=', 'dibayar')->sum('sisa_pokok');
        
        
        return view('angsuran.show', compact('pembiayaan','totalAngsuran','sudahDibayar','sisaAngsuran','outstanding'));
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

    $tanggalBayar =
        now()->format('Y-m-d');


    $preview =
        $this->service->previewDenda(
            $angsuran,
            $tanggalBayar
        );


    return response()->json([

        'id' =>
            $angsuran->id,

        'nama' =>
            $angsuran
                ->pembiayaan
                ->pengajuan
                ->nasabah
                ->nama,

        'nomor_pembiayaan' =>
            $angsuran
                ->pembiayaan
                ->nomor_pembiayaan,

        'angsuran_ke' =>
            $angsuran->angsuran_ke,

        'tanggal_jatuh_tempo' =>
            $angsuran
                ->tanggal_jatuh_tempo
                ->format('d-m-Y'),

        'status' =>
            ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $angsuran->status
                )
            ),

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
            (int) $angsuran->total_angsuran,

        'total_terbayar' =>
            (int) $angsuran->total_terbayar,

        'total_terbayar_format' =>
            number_format(
                $angsuran->total_terbayar,
                0,
                ',',
                '.'
            ),

        'sisa_tagihan' =>
            (int) $angsuran->sisa_tagihan,

        'sisa_tagihan_format' =>
            number_format(
                $angsuran->sisa_tagihan,
                0,
                ',',
                '.'
            ),


        /*
        |--------------------------------------------------------------------------
        | DENDA
        |--------------------------------------------------------------------------
        */

        'hari_terlambat' =>
            $preview['hari_terlambat'],

        'denda' =>
            $preview['denda'],

        'denda_format' =>
            number_format(
                $preview['denda'],
                0,
                ',',
                '.'
            ),


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        'admin_keterlambatan' =>
            $preview[
                'admin_keterlambatan'
            ],

        'admin_keterlambatan_format' =>
            number_format(
                $preview[
                    'admin_keterlambatan'
                ],
                0,
                ',',
                '.'
            ),


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAMBAHAN
        |--------------------------------------------------------------------------
        */

        'total_tambahan' =>
            $preview[
                'total_tambahan'
            ],

        'total_tambahan_format' =>
            number_format(
                $preview[
                    'total_tambahan'
                ],
                0,
                ',',
                '.'
            ),

        'metode' =>
            'tunai',
    ]);
}

    public function previewDenda(
    Request $request,
    Angsuran $angsuran
) {
    $tanggalBayar =
        $request->input(
            'tanggal_bayar',
            now()->format('Y-m-d')
        );


    $result =
        $this->service->previewDenda(
            $angsuran,
            $tanggalBayar
        );


    return response()->json([

        'hari_terlambat' =>
            $result['hari_terlambat'],

        'denda' =>
            $result['denda'],

        'denda_format' =>
            number_format(
                $result['denda'],
                0,
                ',',
                '.'
            ),

        'admin_keterlambatan' =>
            $result[
                'admin_keterlambatan'
            ],

        'admin_keterlambatan_format' =>
            number_format(
                $result[
                    'admin_keterlambatan'
                ],
                0,
                ',',
                '.'
            ),

        'total_tambahan' =>
            $result[
                'total_tambahan'
            ],

        'total_tambahan_format' =>
            number_format(
                $result[
                    'total_tambahan'
                ],
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
