<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Services\PdfService;
use App\Services\PembayaranAngsuranService;
use Illuminate\Http\Request;

class AngsuranController extends Controller
{
    public function __construct(protected PembayaranAngsuranService $service)
    {
    }

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

   
    public function store(Request $request, Angsuran $angsuran)
    {
        $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar'  => 'required|numeric|min:1',
            'metode'        => 'required|in:tunai,transfer',
            'keterangan'    => 'nullable|string',
        ]);

        try {

            $result = $this->service->bayar(
                $angsuran,
                $request->all()
            );

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil.',
                'row'     => $result['row'],
                'summary' => $result['summary'],
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
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
        $angsuran->load(['pembiayaan.pengajuan.nasabah']);

        return response()->json([
            'id' => $angsuran->id,
            'nama' => $angsuran->pembiayaan->pengajuan->nasabah->nama,
            'nomor_pembiayaan' => $angsuran->pembiayaan->nomor_pembiayaan,
            'angsuran_ke' => $angsuran->angsuran_ke,
            'tanggal_jatuh_tempo' => $angsuran->tanggal_jatuh_tempo->format('d-m-Y'),
            'status' => ucfirst(str_replace('_', ' ', $angsuran->status)),
            'pokok' => number_format($angsuran->pokok_angsuran,0,',','.'),
            'bunga' => number_format($angsuran->bunga_angsuran,0,',','.'),
            'total' => number_format($angsuran->total_angsuran,0,',','.'),
            'total_angsuran' => $angsuran->total_angsuran,
            'total_terbayar' => $angsuran->total_terbayar,
            'total_terbayar_format' => number_format($angsuran->total_terbayar,0,',','.'),
            'sisa_tagihan' => $angsuran->sisa_tagihan,
            'sisa_tagihan_format' => number_format($angsuran->sisa_tagihan,0,',','.'),
            'denda' => number_format($angsuran->denda,0,',','.'),
            'metode' => 'tunai',
        ]);
    }


    public function getHistory(Angsuran $angsuran)
    {
        $angsuran->load(['pembiayaan.pengajuan.nasabah','pembayaranAngsurans.creator']);
    
        $history = $angsuran->pembayaranAngsurans->sortBy('tanggal_bayar')->values()
            ->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'nomor'             => $item->nomor_pembayaran,
                    'tanggal'           => \Carbon\Carbon::parse($item->tanggal_bayar)->format('d-m-Y'),
                    'jumlah_bayar'      => $item->jumlah_bayar,
                    'jumlah_bayar_format' => number_format($item->jumlah_bayar,0,',','.'),
                    'denda'             => $item->denda,
                    'denda_format'      => number_format($item->denda,0,',','.'),
                    'diskon'            => $item->diskon,
                    'diskon_format'     => number_format($item->diskon,0,',','.'),
                    'total'             => $item->total_dibayar,
                    'total_format'      => number_format($item->total_dibayar,0,',','.'),
                    'metode'            => ucfirst($item->metode),
                    'user'              => optional($item->creator)->name,
                ];
            });
        return response()->json([
            'nama'              => $angsuran->pembiayaan->pengajuan->nasabah->nama,
            'nomor_pembiayaan'  => $angsuran->pembiayaan->nomor_pembiayaan,
            'angsuran_ke'       => $angsuran->angsuran_ke,
            'status'            => ucfirst(str_replace('_', ' ', $angsuran->status)),
            'total_tagihan'     => number_format($angsuran->total_angsuran,0,',','.'),
            'total_terbayar'    => number_format($angsuran->total_terbayar,0,',','.'),
            'sisa_tagihan'      => number_format($angsuran->sisa_tagihan,0,',','.'),
            'history'           => $history,
        ]);
    }

    public function cetakHistory(Angsuran $angsuran,PdfService $pdfService){
        // return $pdfService->history($angsuran)->stream('History-'.$angsuran->pembiayaan->nomor_pembiayaan.'.pdf');
        $filename = 'History-' .str_replace('/', '-', $angsuran->pembiayaan->nomor_pembiayaan) .'.pdf';
        return $pdfService->history($angsuran)->stream($filename);
    }
}
