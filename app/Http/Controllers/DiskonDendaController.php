<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\PengajuanDiskonDenda;
use App\Services\PembayaranAngsuranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiskonDendaController extends Controller
{
    public function __construct(
        protected PembayaranAngsuranService $pembayaranService
    ) {
    }


    /**
     * Form pengajuan diskon denda
     */
    public function create(Angsuran $angsuran)
    {
        /*
        |--------------------------------------------------------------------------
        | LOAD DATA
        |--------------------------------------------------------------------------
        */

        $angsuran->load([
            'pembiayaan.pengajuan.nasabah',
        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK STATUS
        |--------------------------------------------------------------------------
        */

        if ($angsuran->status === 'dibayar') {
            abort(403, 'Angsuran ini sudah lunas.');
        }


        /*
        |--------------------------------------------------------------------------
        | PREVIEW DENDA
        |--------------------------------------------------------------------------
        |
        | Denda dihitung oleh service.
        | Jangan menghitung denda di Blade.
        |
        */

        $preview = $this->pembayaranService->previewDenda(
            $angsuran
        );


        /*
        |--------------------------------------------------------------------------
        | CEK PENGAJUAN PENDING
        |--------------------------------------------------------------------------
        */

        $pengajuanPending = $angsuran
            ->pengajuanDiskonDendaPending;


        return view(
            'diskon-denda.create',
            compact(
                'angsuran',
                'preview',
                'pengajuanPending'
            )
        );
    }


    /**
     * Simpan pengajuan diskon denda
     */
    public function store(Request $request,Angsuran $angsuran) 
    {

        /*
        |--------------------------------------------------------------------------
        | CEK STATUS
        |--------------------------------------------------------------------------
        */

        if ($angsuran->status === 'dibayar') {
            return back()
                ->with('error', 'Angsuran ini sudah lunas.');
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI INPUT
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'diskon_denda' => [
                'required',
                'numeric',
                'min:1',
            ],

            'alasan' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | HITUNG ULANG DENDA DARI SERVER
        |--------------------------------------------------------------------------
        */

        $preview = $this->pembayaranService->previewDenda(
            $angsuran
        );


        $denda = (int) $preview['denda_tersisa'];


        /*
        |--------------------------------------------------------------------------
        | VALIDASI DENDA
        |--------------------------------------------------------------------------
        */

        if ($denda <= 0) {

            return back()
                ->withErrors([
                    'diskon_denda' =>
                        'Angsuran ini tidak memiliki denda.'
                ])
                ->withInput();
        }


        $diskon = (int) $validated['diskon_denda'];


        /*
        |--------------------------------------------------------------------------
        | DISKON TIDAK BOLEH MELEBIHI DENDA
        |--------------------------------------------------------------------------
        */

        if ($diskon > $denda) {

            return back()
                ->withErrors([
                    'diskon_denda' =>
                        'Diskon denda tidak boleh melebihi denda.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | CEK PENGAJUAN PENDING
        |--------------------------------------------------------------------------
        */

        $pending = $angsuran
            ->pengajuanDiskonDendaPending;

        if ($pending) {

            return back()
                ->with(
                    'error',
                    'Masih terdapat pengajuan diskon denda yang menunggu persetujuan pimpinan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | DENDA SETELAH DISKON
        |--------------------------------------------------------------------------
        */

        $dendaSetelahDiskon = max(
            0,
            $denda - $diskon
        );


        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $angsuran,
            $denda,
            $diskon,
            $dendaSetelahDiskon,
            $validated
        ) {

            PengajuanDiskonDenda::create([

                'angsuran_id' =>
                    $angsuran->id,

                'denda' =>
                    $denda,

                'diskon_denda' =>
                    $diskon,

                'denda_setelah_diskon' =>
                    $dendaSetelahDiskon,

                'alasan' =>
                    $validated['alasan'],

                'status' =>
                    'pending',

                'diajukan_oleh' =>
                    auth()->id(),

            ]);
        });


        return redirect()
            ->route(
                'angsuran.show',
                $angsuran->pembiayaan_id
            )
            ->with(
                'success',
                'Pengajuan diskon denda berhasil diajukan dan menunggu persetujuan pimpinan.'
            );
    }

    public function indexApproval()
    {
        /*
        |--------------------------------------------------------------------------
        | HANYA PIMPINAN
        |--------------------------------------------------------------------------
        */

        if (!auth()->user()->hasAnyRole(['komisaris','direktur','kacab',])) 
        {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | PENGAJUAN PENDING
        |--------------------------------------------------------------------------
        */

        $pengajuans = PengajuanDiskonDenda::with([
            'angsuran.pembiayaan.pengajuan.nasabah',
            'diajukanOleh',
        ])
        ->where('status', 'pending')
        ->latest()
        ->paginate(15);


        return view(
            'diskon-denda.approval',
            compact('pengajuans')
        );
    }

    public function approve(PengajuanDiskonDenda $pengajuan) 
    {
        /*
        |--------------------------------------------------------------------------
        | HANYA PIMPINAN
        |--------------------------------------------------------------------------
        */

        if (!auth()->user()->hasAnyRole(['komisaris','direktur','kacab',])) 
        {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | HARUS PENDING
        |--------------------------------------------------------------------------
        */

        if ($pengajuan->status !== 'pending') {

            return back()->with(
                'error',
                'Pengajuan ini sudah diproses.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | APPROVAL
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($pengajuan) {

            $pengajuan->update([

                'status' =>
                    'disetujui',

                'disetujui_oleh' =>
                    auth()->id(),

                'disetujui_at' =>
                    now(),

                'catatan_approval' =>
                    null,

            ]);
        });


        return back()->with(
            'success',
            'Pengajuan diskon denda berhasil disetujui.'
        );
    }

    public function reject(Request $request,PengajuanDiskonDenda $pengajuan) 
    {
        /*
        |--------------------------------------------------------------------------
        | HANYA PIMPINAN
        |--------------------------------------------------------------------------
        */

        if (!auth()->user()->hasAnyRole(['komisaris','direktur','kacab',])) 
        {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'catatan_approval' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | HARUS PENDING
        |--------------------------------------------------------------------------
        */

        if ($pengajuan->status !== 'pending') {

            return back()->with(
                'error',
                'Pengajuan ini sudah diproses.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TOLAK
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $pengajuan,
            $validated
        ) {

            $pengajuan->update([

                'status' =>
                    'ditolak',

                'disetujui_oleh' =>
                    auth()->id(),

                'disetujui_at' =>
                    now(),

                'catatan_approval' =>
                    $validated['catatan_approval'],

            ]);
        });


        return back()->with(
            'success',
            'Pengajuan diskon denda ditolak.'
        );
    }
}