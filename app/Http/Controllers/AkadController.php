<?php

namespace App\Http\Controllers;

use App\Models\Akad;
use App\Models\Pembiayaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\GenerateDokumenService;

class AkadController extends Controller
{
  
    public function create(Pembiayaan $pembiayaan)
    {
        abort_if($pembiayaan->status != 'jadwal_generated',403,'Pembiayaan belum siap akad.');

        if($pembiayaan->akad){
            return redirect()
                    ->route('akad.edit',$pembiayaan->akad);
        }

        $pembiayaan->load(['pengajuan.nasabah','pengajuan.marketing','pengajuan.cabang','angsurans',]);

        $nomorAkad = $this->generateNomorAkad();

        return view('akad.create',compact('pembiayaan','nomorAkad'));
    }

   
    public function store(Request $request, Pembiayaan $pembiayaan)
    {
        $request->validate([
            'nomor_akad'=>'required',
            'tanggal_akad'=>'required|date',
            'tempat_akad'=>'required|string|max:255',
            'nomor_perjanjian'=>'nullable|max:255',
            'catatan'=>'nullable',
        ]);

        DB::transaction(function() use($request,$pembiayaan){
            Akad::create([
                'pembiayaan_id'=>$pembiayaan->id,
                'nomor_akad'=>$request->nomor_akad,
                'tanggal_akad'=>$request->tanggal_akad,
                'tempat_akad'=>$request->tempat_akad,
                'nomor_perjanjian'=>$request->nomor_perjanjian,
                'catatan'=>$request->catatan,
                'status'=>'draft',
                'created_by'=>auth()->id(),
            ]);

            $pembiayaan->update(['status'=>'akad']);

        });

        return redirect()->route('pembiayaan.index')->with('success','Akad berhasil dibuat.');
    }


    public function show(Akad $akad)
    {
        $akad->load(['pembiayaan','pembiayaan.pengajuan.nasabah','pembiayaan.pengajuan.marketing.user','pembiayaan.pengajuan.cabang','pembiayaan.angsurans',]);

        $angsuranPertama = $akad->pembiayaan->angsurans->sortBy('angsuran_ke')->first();
        $angsuranTerakhir = $akad->pembiayaan->angsurans->sortByDesc('angsuran_ke')->first();

        return view('akad.show', compact('akad','angsuranPertama','angsuranTerakhir'));
}

    public function edit(Akad $akad)
    {
        $akad->load(['pembiayaan','pembiayaan.pengajuan.nasabah','pembiayaan.pengajuan.marketing','pembiayaan.pengajuan.cabang',]);
    
        return view('akad.edit',compact('akad'));
    }

    public function update(Request $request,Akad $akad)
    {
        $request->validate([
        'tanggal_akad'=>'required|date',
        'tempat_akad'=>'required|max:255',
        'nomor_perjanjian'=>'nullable|max:255',
        'catatan'=>'nullable',
        ]);

        $akad->update([
        'tanggal_akad'=>$request->tanggal_akad,
        'tempat_akad'=>$request->tempat_akad,
        'nomor_perjanjian'=>$request->nomor_perjanjian,
        'catatan'=>$request->catatan,
        ]);

        return redirect()->route('akad.show',$akad)->with('success','Akad berhasil diperbarui.');

    }

    private function generateNomorAkad()
    {
        $last = Akad::max('id') + 1;
        return 'AKD/' .date('Y') .'/' .str_pad($last,5,'0',STR_PAD_LEFT);
    }

  /*   public function generate(Akad $akad, GenerateDokumenService $generator)
    {
        $result = $generator->generateAkad($akad);
        
        return redirect()->back()->with('success','Dokumen berhasil dibuat.')
                        ->with('download',asset($result['public_path']));
    
    } */

    public function generate(Akad $akad, GenerateDokumenService $generator)
    {
        $generator->generateAkad($akad);
        return redirect()->route('akad.show',$akad)->with('success','Dokumen berhasil dibuat.');
    
    }

    public function downloadWord(Akad $akad)
    {
        abort_if(!$akad->file_word,404,'Dokumen belum tersedia.');

        return response()->download(storage_path('app/public/akad/'.$akad->file_word));
    }
}
