<?php

namespace App\Http\Controllers;

use App\Models\Akad;
use App\Models\Pencairan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    public function index()
    {
        $pencairans = Pencairan::with([
            'akad.pembiayaan.pengajuan.nasabah',
            'creator'
        ])
        ->latest()
        ->paginate(15);

        return view('pencairan.index', compact('pencairans'));
    }

    public function create(Akad $akad)
    {
        $akad->load(['pembiayaan','pembiayaan.pengajuan.nasabah','pembiayaan.pengajuan.marketing.user','pembiayaan.pengajuan.cabang','pencairan',]);
    
       abort_if($akad->status != 'signed' ||$akad->pembiayaan->status != 'signed',403,'Akad belum ditandatangani.');
    
        if ($akad->pencairan) {return redirect()
                ->route('pencairan.show', $akad->pencairan)
                ->with('warning', 'Pencairan sudah pernah dilakukan.');
        }
    
        return view('pencairan.create', compact('akad'));
    }
   
    public function store(Request $request, Akad $akad)
    {
       abort_if($akad->status != 'signed' ||$akad->pembiayaan->status != 'signed',403,'Akad belum ditandatangani.');

        $request->validate([
            'tanggal_pencairan' => 'required|date',
            'metode'            => 'required|in:tunai,transfer',
            'bank'              => 'nullable|string|max:100',
            'no_rekening'       => 'nullable|string|max:100',
            'atas_nama'         => 'nullable|string|max:100',
            'keterangan'        => 'nullable|string|max:1000',
        ]);

        if ($akad->pencairan) {return back()->with('error','Pencairan sudah pernah dibuat.');
        }

        DB::transaction(function () use ($request, $akad) {
            $pembiayaan = $akad->pembiayaan;
            Pencairan::create([
                'akad_id'           => $akad->id,
                'nomor_pencairan'   => $this->generateNomor(),
                'tanggal_pencairan' => $request->tanggal_pencairan,
                'jumlah_dicairkan'  => $pembiayaan->dana_diterima,
                'metode'            => $request->metode,
                'bank'              => $request->bank,
                'no_rekening'       => $request->no_rekening,
                'atas_nama'         => $request->atas_nama,
                'keterangan'        => $request->keterangan,
                'created_by'        => auth()->id(),
            ]);

            $pembiayaan->update([
                'status'             => 'dicairkan',
                'tanggal_pencairan'  => $request->tanggal_pencairan,
            ]);
        });
        
        $akad->load('pencairan');

        return redirect()->route('pencairan.show', $akad->pencairan)->with('success','Pencairan berhasil disimpan.');
    }

    /**
     * Detail pencairan
     */
    public function show(Pencairan $pencairan)
    {
        $pencairan->load(['creator','akad','akad.pembiayaan','akad.pembiayaan.pengajuan','akad.pembiayaan.pengajuan.nasabah',
            'akad.pembiayaan.pengajuan.marketing','akad.pembiayaan.pengajuan.cabang',]);

        return view('pencairan.show', compact('pencairan'));
    }

    /**
     * Generate nomor pencairan
     */
    private function generateNomor()
    {
        $prefix = 'PCR';

        $tahun = date('Y');

        $last = Pencairan::whereYear('created_at', $tahun)->count() + 1;

        return sprintf("%s/%s/%05d",$prefix,$tahun,$last);
    }
}
