<?php

namespace App\Http\Controllers;

use App\Models\Pelunasan;
use App\Models\Pembiayaan;
use App\Services\PdfService;
use App\Services\PelunasanService;
use Illuminate\Http\Request;

class PelunasanController extends Controller
{
    public function __construct(protected PelunasanService $service) {}

    public function create(Pembiayaan $pembiayaan)
    {
        $pembiayaan->load(['pengajuan.nasabah','angsurans']);

        $sisaPokok = $pembiayaan->angsurans()->where('status','!=','dibayar')->sum('pokok_angsuran');

        $sisaBunga = $pembiayaan->angsurans()->where('status','!=','dibayar')->sum('bunga_angsuran');

        $denda = $pembiayaan->angsurans()->where('status','jatuh_tempo')->sum('denda');

        return view('pelunasan.create', compact('pembiayaan','sisaPokok','sisaBunga','denda'));
    }

    public function store(Request $request,Pembiayaan $pembiayaan) {

        $validated = $request->validate([
            'tanggal_pelunasan'=>['required','date'],
            'diskon'=>['nullable','numeric','min:0'],
            'keterangan'=>['nullable','string'],
        ]);

        $this->service->store($pembiayaan,$validated);

        return redirect()->route('operasional.show', $pembiayaan)
            ->with('success','Pelunasan berhasil disimpan.');
    }

    public function show(Pelunasan $pelunasan)
    {
        $pelunasan->load(['creator','pembiayaan','pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.marketing','pembiayaan.pengajuan.cabang','pembiayaan.akad','pembiayaan.akad.pencairan']);

        return view('pelunasan.show',compact('pelunasan'));
    }
   
    public function cetak(Pelunasan $pelunasan, PdfService $pdfService)
    {
        $pelunasan->load(['creator','pembiayaan','pembiayaan.pengajuan.nasabah','pembiayaan.pengajuan.marketing',
            'pembiayaan.pengajuan.cabang','pembiayaan.akad','pembiayaan.akad.pencairan',]);
    
        return $pdfService->pelunasan($pelunasan)->stream('Pelunasan-' .$pelunasan->nomor_pelunasan .'.pdf');
    }
}
