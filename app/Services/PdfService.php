<?php

namespace App\Services;

use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use Barryvdh\DomPDF\Facade\Pdf;

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
}