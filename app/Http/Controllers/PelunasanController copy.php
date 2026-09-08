<?php

namespace App\Http\Controllers;

use App\Models\Pelunasan;
use App\Models\Pembiayaan;
use App\Services\PdfService;
use App\Services\PelunasanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelunasanController extends Controller
{
    public function __construct(protected PelunasanService $service) {}


    public function create(Pembiayaan $pembiayaan)
    {
        $pembiayaan->load([
            'pengajuan.nasabah',
            'angsurans'
        ]);

        /*
         * Hitung seluruh komponen pelunasan
         * melalui service.
         */
        $perhitungan = $this->service->hitungPelunasan(
            $pembiayaan,
            now()->toDateString()
        );

        return view(
            'pelunasan.create',
            compact(
                'pembiayaan',
                'perhitungan'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PENGAJUAN PELUNASAN
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Pembiayaan $pembiayaan
    ) {

        $validated = $request->validate([

            'tanggal_pelunasan' => [
                'required',
                'date'
            ],

            'diskon' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'alasan_diskon' => [
                'nullable',
                'string'
            ],

            'keterangan' => [
                'nullable',
                'string'
            ],
        ]);

        $pelunasan = $this->service->store(
            $pembiayaan,
            $validated
        );

        /*
         * Jika menggunakan diskon,
         * berarti menunggu persetujuan.
         */
        if ($pelunasan->jenis_pelunasan === 'dengan_diskon') {

            $message =
                'Pengajuan pelunasan dengan diskon berhasil disimpan dan menunggu persetujuan pimpinan.';

        } else {

            $message =
                'Pengajuan pelunasan berhasil disimpan dan siap dibayar.';
        }

        return redirect()
            ->route(
                'operasional.show',
                $pembiayaan
            )
            ->with(
                'success',
                $message
            );
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
