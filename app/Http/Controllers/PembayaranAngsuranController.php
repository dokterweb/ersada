<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use App\Services\PdfService;
use Illuminate\Http\Request;

class PembayaranAngsuranController extends Controller
{
    public function __construct(
        protected PdfService $pdfService
    ){}
    
    public function index(Request $request)
    {
        $query = Angsuran::with(['pembiayaan.pengajuan.nasabah']);
    
        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        // Filter Nama Debitur
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
    
            $query->whereHas('pembiayaan.pengajuan.nasabah', function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%");
            });
        }
    
        $angsurans = $query->orderBy('tanggal_jatuh_tempo')->paginate(20);
    
        return view('angsuran.index', compact('angsurans'));
    }

    /**
     * Form pembayaran
     */
    public function create(Angsuran $angsuran)
    {

    }

    /**
     * Simpan pembayaran
     */
    public function store(Request $request, Angsuran $angsuran)
    {

    }

    /**
     * Detail pembayaran
     */
    public function show(PembayaranAngsuran $pembayaranAngsuran)
    {

    }

    /**
     * Riwayat pembayaran
     */
    public function history(Angsuran $angsuran)
    {

    }

    public function cetak(PembayaranAngsuran $pembayaran)
    {
        $filename = 'Kwitansi-' .str_replace('/', '-', $pembayaran->nomor_pembayaran) .'.pdf';
        return $this->pdfService->kwitansi($pembayaran)->stream($filename);
    }

   
}
