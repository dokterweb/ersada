<?php

namespace App\Services;

use App\Models\Angsuran;
use App\Models\Pelunasan;
use App\Models\PembayaranAngsuran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class PdfService
{
    /**
     * Cetak 1 transaksi pembayaran
     */
    public function kwitansi(PembayaranAngsuran $pembayaran)
    {
        // $pembayaran->load(['creator','angsuran','angsuran.pembiayaan','angsuran.pembiayaan.cabang','angsuran.pembiayaan.pengajuan.nasabah',]);
        $pembayaran->load([
            'creator',
            'angsuran',
            'angsuran.pembiayaan',
            'angsuran.pembiayaan.pengajuan',
            'angsuran.pembiayaan.pengajuan.cabang',
            'angsuran.pembiayaan.pengajuan.nasabah',
        ]);

        return Pdf::loadView('pdf.kwitansi',compact('pembayaran'))->setPaper('A4', 'portrait');
    }

    /**
     * Cetak history pembayaran
     */
    public function history(Angsuran $angsuran)
    {
        // $angsuran->load(['pembiayaan','pembiayaan.cabang','pembiayaan.pengajuan.nasabah','pembayaranAngsurans.creator']);
        $angsuran->load([
            'pembiayaan',
            'pembiayaan.pengajuan',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.nasabah',
            'pembayaranAngsurans.creator',
        ]);
        return Pdf::loadView('pdf.history-pembayaran',compact('angsuran'))->setPaper('A4', 'landscape');
    }

    public function pelunasan(Pelunasan $pelunasan)
    {
        $pelunasan->load(['creator','pembiayaan','pembiayaan.pengajuan.nasabah','pembiayaan.pengajuan.marketing',
            'pembiayaan.pengajuan.cabang','pembiayaan.akad','pembiayaan.akad.pencairan',]);
    
        return Pdf::loadView('pdf.pelunasan',compact('pelunasan'))->setPaper('A4', 'portrait');
    }

    public function laporanPembiayaan(Collection $pembiayaans, Request $request )
    {
        $pdf = Pdf::loadView(
            'pdf.laporan-pembiayaan',
            [
                'pembiayaans' => $pembiayaans,
                'request'      => $request,
                'tanggalCetak' => now(),
            ]
        );
    
        $pdf->setPaper('A4', 'landscape');
    
        return $pdf->stream('Laporan-Pembiayaan.pdf');
    }

    public function laporanPencairan(Collection $pencairans, Request $request)
    {
        $pdf = Pdf::loadView('pdf.laporan-pencairan',
            [
                'pencairans' => $pencairans,
                'request' => $request,
                'tanggalCetak' => now(),
            ]
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Pencairan.pdf');
    }

    public function laporanAngsuran(Collection $angsurans, Request $request)
    {
        $pdf = Pdf::loadView('pdf.laporan-angsuran',
            [
                'angsurans' => $angsurans,
                'request' => $request,
                'tanggalCetak' => now(),
            ]
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Angsuran.pdf');
    }

    public function laporanPelunasan(Collection $pelunasans, Request $request)
    {
        $pdf = Pdf::loadView('pdf.laporan-pelunasan',
            [
                'pelunasans' => $pelunasans,
                'request' => $request,
                'tanggalCetak' => now(),
            ]
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Pelunasan.pdf');
    }

    public function laporanOutstanding(Collection $pembiayaans, Request $request)
    {
        $pdf = Pdf::loadView('pdf.laporan-outstanding',
            [
                'pembiayaans' => $pembiayaans,
                'request' => $request,
                'tanggalCetak' => now(),
            ]
        );
    
        $pdf->setPaper('A4', 'landscape');
    
        return $pdf->stream('Laporan-Outstanding.pdf');
    }

    public function laporanJatuhTempo(Collection $angsurans, Request $request)
    {
        $pdf = Pdf::loadView('pdf.laporan-jatuh-tempo',
            [
                'angsurans' => $angsurans,
                'request' => $request,
                'tanggalCetak' => now(),
            ]
        );
    
        $pdf->setPaper('A4', 'landscape');
    
        return $pdf->stream('Laporan-Jatuh-Tempo.pdf');
    }

    public function laporanNpl(Collection $angsurans, Request $request)
    {
        $pdf = Pdf::loadView('pdf.laporan-npl',
            [
                'angsurans'    => $angsurans,
                'request'      => $request,
                'tanggalCetak' => now(),
            ]
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-NPL.pdf');
    }

}