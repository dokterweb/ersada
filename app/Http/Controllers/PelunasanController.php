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
        // Cegah pengajuan pelunasan diskon ganda
        // selama pengajuan sebelumnya menunggu persetujuan.
        $pelunasanMenunggu = Pelunasan::where(
            'pembiayaan_id',
            $pembiayaan->id
        )
            ->where('jenis_pelunasan', 'dengan_diskon')
            ->where('status', 'menunggu_persetujuan')
            ->exists();

        if ($pelunasanMenunggu) {
            return redirect()
                ->route('operasional.show', $pembiayaan)
                ->with(
                    'error',
                    'Pengajuan pelunasan dengan diskon masih menunggu persetujuan pimpinan. Silakan tunggu keputusan pimpinan.'
                );
        }

        $pembiayaan->load([
            'pengajuan.nasabah',
            'angsurans'
        ]);

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


    public function store(Request $request, Pembiayaan $pembiayaan)
    {
        // Cegah pengajuan pelunasan diskon ganda.
        $pelunasanMenunggu = Pelunasan::where(
            'pembiayaan_id',
            $pembiayaan->id
        )
            ->where('jenis_pelunasan', 'dengan_diskon')
            ->where('status', 'menunggu_persetujuan')
            ->exists();

        if ($pelunasanMenunggu) {
            return redirect()
                ->route('operasional.show', $pembiayaan)
                ->with(
                    'error',
                    'Pengajuan pelunasan dengan diskon masih menunggu persetujuan pimpinan.'
                );
        }

        // Lanjutkan kode store() lama Anda di bawah sini.
    }


    public function show(Pelunasan $pelunasan)
    {
        $pelunasan->load([
            'pembiayaan.pengajuan.nasabah',
            'creator',
            'approver',
            'payer',
            'angsurans' => function ($q) {
                $q->orderBy('angsuran_ke');
            },
        ]);

        $pengajuan = $pelunasan->pembiayaan->pengajuan;
        return view('pelunasan.show',compact('pelunasan','pengajuan'));
    }
   
    public function cetak(Pelunasan $pelunasan, PdfService $pdfService)
    {
        $pelunasan->load(['creator','pembiayaan','pembiayaan.pengajuan.nasabah','pembiayaan.pengajuan.marketing',
            'pembiayaan.pengajuan.cabang','pembiayaan.akad','pembiayaan.akad.pencairan',]);
    
        return $pdfService->pelunasan($pelunasan)->stream('Pelunasan-' .$pelunasan->nomor_pelunasan .'.pdf');
    }

    public function approve(Request $request,Pelunasan $pelunasan) 
    {

        $validated = $request->validate([

            'diskon_disetujui' => ['required','numeric','min:0'],
            'catatan_approval' => ['nullable','string'],
        ]);

        $this->service->approve($pelunasan,$validated);

        return redirect()->route('pelunasan.show',$pelunasan)
            ->with('success','Pengajuan pelunasan berhasil disetujui.');
    }

    public function reject(Request $request,Pelunasan $pelunasan) 
    {

        $validated = $request->validate([

            'catatan_approval' => [
                'required',
                'string'
            ],

        ]);

        $this->service->reject(
            $pelunasan,
            $validated
        );

        return redirect()
            ->route(
                'pelunasan.show',
                $pelunasan
            )
            ->with(
                'success',
                'Pengajuan pelunasan berhasil ditolak.'
            );
    }

    public function bayar(
        Request $request,
        Pelunasan $pelunasan
    ) {
        $validated = $request->validate([
            'tanggal_bayar' => [
                'required',
                'date',
            ],

            'jumlah_bayar' => [
                'required',
                'numeric',
                'min:1',
            ],
        ]);

        try {

            $pelunasan = $this->service->bayar(
                $pelunasan,
                $validated
            );

            return redirect()
                ->route('pelunasan.show', $pelunasan)
                ->with(
                    'success',
                    'Pembayaran pelunasan berhasil. Pembiayaan telah dinyatakan lunas.'
                );

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function persetujuanDiskon()
    {
        $pelunasans = Pelunasan::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.marketing.user',
            'pembiayaan.pengajuan.cabang',
            'creator',
        ])
        ->where('status', 'menunggu_persetujuan')
        ->where('jenis_pelunasan', 'dengan_diskon')
        ->latest()
        ->paginate(15);

        return view(
            'pelunasan.persetujuan-diskon',
            compact('pelunasans')
        );
    }

    public function pengajuanDiskon()
    {
        $pelunasans = Pelunasan::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.cabang',
            'creator',
            'approver',
        ])
        ->where('jenis_pelunasan', 'dengan_diskon')
        ->where('created_by', auth()->id())
        ->latest()
        ->paginate(15);

        return view(
            'pelunasan.pengajuan-diskon',
            compact('pelunasans')
        );
    }

    public function bayarTanpaDiskon(Request $request,Pelunasan $pelunasan) 
    {
        $validated = $request->validate([
            'tanggal_bayar' => [
                'required',
                'date'
            ],
        ]);

        $this->service->bayarTanpaDiskon(
            $pelunasan,
            $validated
        );

        return redirect()
            ->route('pelunasan.show', $pelunasan)
            ->with(
                'success',
                'Pelunasan tanpa diskon berhasil diproses dan siap dibayar.'
            );
    }
}
