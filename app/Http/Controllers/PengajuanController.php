<?php

namespace App\Http\Controllers;


use App\Http\Requests\PengajuanStep1Request;
use App\Http\Requests\PengajuanStep3Request;
use App\Http\Requests\PengajuanStep4Request;
use App\Models\AnalisaPengajuan;
use App\Models\Cabang;
use App\Models\Dokumen_pengajuan;
use App\Models\JaminanPengajuan;
use App\Models\KapitalPengajuan;
use App\Models\Nasabah;
use App\Models\Pekerjaan_nasabah;
use App\Models\Pekerjaan_referensi;
use App\Models\Pengajuan;
use App\Models\Referensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    public function createStep1()
    {
        return view('pengajuans.step1');
    }

    public function storeStep1(PengajuanStep1Request $request)
    {
        DB::beginTransaction();

        try {
            $marketing = auth()->user()->karyawan;
            $nomor = 'PGJ-' . now()->format('YmdHis');

            $pengajuan = Pengajuan::create([
                'nomor_pengajuan'       => $nomor,
                'cabang_id'             => $marketing->cabang_id,
                'marketing_id'          => $marketing->id,
                'status'                => 'draft',
                'current_step'          => 2,
                'tanggal_pengajuan'     => $request->tanggal_pengajuan,
                'nominal_pengajuan'     => $request->nominal_pengajuan,
                'tenor'                 => $request->tenor,
                'kategori_nasabah'      => $request->kategori_nasabah,
                'tujuan_pinjaman'       => $request->tujuan_pinjaman,
                'catatan'               => $request->catatan,
            ]);

            DB::commit();

            // redirect step 2
            
            return redirect()->route('pengajuan.step2',$pengajuan->id)
                ->with('success','Step 1 berhasil disimpan');

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    public function editStep1(Pengajuan $pengajuan)
    {
        /*
        kalau sudah submit final
        jangan boleh edit
        */

        if($pengajuan->status != 'draft'){
            abort(403,'Pengajuan sudah dikirim');
        }

        return view('pengajuans.step1',compact('pengajuan'));
    }

    public function updateStep1(PengajuanStep1Request $request,Pengajuan $pengajuan)
    {
        DB::beginTransaction();
    
        try {
    
            /*
            kategori lama sebelum update
            */
            $oldKategori = $pengajuan->kategori_nasabah;
            $newKategori = $request->kategori_nasabah;
            /*
            update step1
            */
            $pengajuan->update([
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'nominal_pengajuan' => $request->nominal_pengajuan,
                'tenor' => $request->tenor,
                'kategori_nasabah' => $newKategori,
                'tujuan_pinjaman' => $request->tujuan_pinjaman,
                'catatan' => $request->catatan,
            ]);
    
            /*
            =====================================
            JIKA KATEGORI BERUBAH
            =====================================
            */
            if ($oldKategori != $newKategori) {
                /*
                CASE 1
                payroll → non_payroll
                hapus dokumen payroll specific
                */
                if (
                    $oldKategori == 'payroll'
                    &&
                    $newKategori == 'non_payroll'
                ) {
                    $invalidDocs = ['atm_gaji','bpjs','sk_kerja'];
                    $docs = Dokumen_pengajuan::where('pengajuan_id',$pengajuan->id)
                    ->whereIn('jenis_dokumen',$invalidDocs)->get();
    
                    foreach ($docs as $doc) {
                        /*
                        hapus file fisik
                        */
                        Storage::disk('public')->delete($doc->file_path);
                        /*
                        hapus record db
                        */
                        $doc->delete();
                    }
                }
    
                /*
                =====================================
                RESET STATUS DOKUMEN
                =====================================
                */
                $pengajuan->update([
                    /*
                    user wajib cek step4 lagi
                    */
                    'documents_completed' => false,
                    /*
                    rollback wizard
                    */
                    'current_step' => 4
                ]);
    
                DB::commit();
                /*
                langsung ke step4
                */
                return redirect()->route('pengajuan.step4',$pengajuan->id)
                    ->with('warning','Kategori nasabah berubah. Silakan periksa ulang dokumen.');
            }
    
    
            /*
            =====================================
            JIKA TIDAK ADA PERUBAHAN KATEGORI
            =====================================
            */
    
            DB::commit();
    
            return redirect()->route('pengajuan.step2',$pengajuan->id)
                ->with('success','Step 1 berhasil diupdate');
    
        }
        catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    public function step2(Pengajuan $pengajuan)
    {
        if($pengajuan->status != 'draft'){
            abort(403);
        }

        $pengajuan->load(['nasabah','nasabah.pekerjaan']);
        return view('pengajuans.step2',compact('pengajuan'));
    }

    public function storeStep2(Request $request, Pengajuan $pengajuan)
    {
        $validated = $request->validate([
    
            /*
            NASABAH
            */
            'nama' => 'required',
            'nik' => 'required|min:16|max:16',
            'tempat_lahir' => 'required',
            'tgl_lahir' => 'required|date',
            'no_hp' => 'required',
            'alamat' => 'required',
            'status_perkawinan' => 'required',
            'jumlah_tanggungan' => 'required|integer',
            'status_rumah' => 'required',
            'lama_menetap_tahun' => 'required|integer',
            'lama_menetap_bulan' => 'required|integer',
    
            /*
            PEKERJAAN
            */
            'jenis_pekerjaan' => 'required',
            'penghasilan' => 'required|numeric',
            'nama_usaha' => 'nullable',
            'jenis_usaha' => 'nullable',
            'lama_usaha' => 'nullable|integer',
            'jumlah_pegawai' => 'nullable|integer',
            'alamat_usaha' => 'nullable',
            'telpon_usaha' => 'nullable',
            'bangunan_usaha' => 'nullable',
            'status_tempat_usaha' => 'nullable',
            'aktivitas_usaha' => 'nullable'
        ]);
    
    
        DB::beginTransaction();
    
        try {
            // NASABAH satu pengajuan = satu nasabah
            $nasabah = Nasabah::updateOrCreate(
                // unique condition
                ['pengajuan_id' => $pengajuan->id],
                // update data    
                [
                    'nama' => $validated['nama'],
                    'nik' => $validated['nik'],
                    'tempat_lahir' =>$validated['tempat_lahir'],
                    'tgl_lahir' =>$validated['tgl_lahir'],
                    'no_hp' =>$validated['no_hp'],
                    'alamat' =>$validated['alamat'],
                    'status_perkawinan' =>$validated['status_perkawinan'],
                    'jumlah_tanggungan' =>$validated['jumlah_tanggungan'],
                    'status_rumah' =>$validated['status_rumah'],
                    'lama_menetap_tahun' =>$validated['lama_menetap_tahun'],
                    'lama_menetap_bulan' =>$validated['lama_menetap_bulan']
                ]
            );
    
            // PEKERJAAN NASABAH satu nasabah = satu pekerjaan
            Pekerjaan_nasabah::updateOrCreate(
                // unique condition
                ['nasabah_id' => $nasabah->id],
                // update data    
                [
                    'jenis_pekerjaan' =>$validated['jenis_pekerjaan'],
                    'penghasilan' =>$validated['penghasilan'],
                    'nama_usaha' =>$validated['nama_usaha'],
                    'jenis_usaha' =>$validated['jenis_usaha'],
                    'lama_usaha' =>$validated['lama_usaha'],
                    'jumlah_pegawai' =>$validated['jumlah_pegawai'],
                    'alamat_usaha' =>$validated['alamat_usaha'],
                    'telpon_usaha' =>$validated['telpon_usaha'],
                    'bangunan_usaha' =>$validated['bangunan_usaha'],
                    'status_tempat_usaha' =>$validated['status_tempat_usaha'],
                    'aktivitas_usaha' =>$validated['aktivitas_usaha']
                ]
            );
    
            // step tetap 3    
            $pengajuan->update(['current_step' => 3]);
            DB::commit();
            return redirect()->route('pengajuan.step3',$pengajuan->id);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    public function step3(Pengajuan $pengajuan)
    {
        if($pengajuan->status != 'draft'){
            abort(403);
        }
    
        $pengajuan->load(['referensis','referensis.pekerjaan']);
        $pasangan = $pengajuan->referensis->where('jenis','pasangan')->first();
        $penjamin = $pengajuan->referensis->where('jenis','penjamin')->first();
        // $saudaras = $pengajuan->referensis->where('jenis','saudara')->sortBy('urutan');
        $saudaras = $pengajuan->referensis->where('jenis','saudara')->sortBy('urutan')->values();
        return view('pengajuans.step3',compact('pengajuan','pasangan','penjamin','saudaras'));
    }

    public function storeStep3(PengajuanStep3Request $request, Pengajuan $pengajuan)
    {
        DB::beginTransaction();
        try {
            if ($request->has_pasangan) {
                $pasangan = Referensi::updateOrCreate(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'jenis'        => 'pasangan'
                    ],
                    [
                        'nama'          => $request->pasangan['nama'] ?? null,
                        'tempat_lahir'  => $request->pasangan['tempat_lahir'] ?? null,
                        'tgl_lahir'     => $request->pasangan['tgl_lahir'] ?? null,
                    ]
                );
                if ($request->has_pekerjaan_pasangan) {
                    Pekerjaan_referensi::updateOrCreate(
                        [
                            'referensi_id' => $pasangan->id
                        ],
                        [
                            'penghasilan' =>$request->pasangan['pekerjaan']['penghasilan'] ?? null,
                            'nama_usaha' =>$request->pasangan['pekerjaan']['nama_usaha'] ?? null,
                            'jenis_usaha' =>$request->pasangan['pekerjaan']['jenis_usaha'] ?? null,
                            'lama_usaha' =>$request->pasangan['pekerjaan']['lama_usaha'] ?? null,
                            'jumlah_pegawai' =>$request->pasangan['pekerjaan']['jumlah_pegawai'] ?? null,
                            'alamat_usaha' =>$request->pasangan['pekerjaan']['alamat_usaha'] ?? null,
                        ]
                    );
                } else {
                    Pekerjaan_referensi::where('referensi_id',$pasangan->id)->delete();
                }
    
            } else {
                $oldPasangan = Referensi::where('pengajuan_id',$pengajuan->id)->where('jenis','pasangan')->first();
                if ($oldPasangan) {
                    Pekerjaan_referensi::where('referensi_id',$oldPasangan->id)->delete();
                    $oldPasangan->delete();
                }
            }
    
            if ($request->has_penjamin) {
                $penjamin = Referensi::updateOrCreate(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'jenis'        => 'penjamin'
                    ],
                    [
                        'nama' =>$request->penjamin['nama'] ?? null,
                        'tempat_lahir' =>$request->penjamin['tempat_lahir'] ?? null,
                        'tgl_lahir' =>$request->penjamin['tgl_lahir'] ?? null,
                        'hubungan' =>$request->penjamin['hubungan'] ?? null,
                        'no_hp' =>$request->penjamin['no_hp'] ?? null,
                        'alamat' =>$request->penjamin['alamat'] ?? null
                    ]
                );
                if ($request->boolean('has_pekerjaan_penjamin')) {
                
                  /*   dd(
                        $penjamin->id,
                    
                        Pekerjaan_referensi::where(
                            'referensi_id',
                            $penjamin->id
                        )->get()
                    ); */

                    Pekerjaan_referensi::updateOrCreate(
                        [
                            'referensi_id' => $penjamin->id
                        ],
                        [
                            'penghasilan' =>$request->penjamin['pekerjaan']['penghasilan'] ?? null,
                            'nama_usaha' =>$request->penjamin['pekerjaan']['nama_usaha'] ?? null,
                            'jenis_usaha' =>$request->penjamin['pekerjaan']['jenis_usaha'] ?? null,
                            'lama_usaha' =>$request->penjamin['pekerjaan']['lama_usaha'] ?? null,
                            'jumlah_pegawai' =>$request->penjamin['pekerjaan']['jumlah_pegawai'] ?? null,
                            'alamat_usaha' =>$request->penjamin['pekerjaan']['alamat_usaha'] ?? null,
                        ]
                    );
    
                } else {
                    Pekerjaan_referensi::where('referensi_id',$penjamin->id)->delete();
                }
    
            } else {
                $oldPenjamin = Referensi::where('pengajuan_id',$pengajuan->id)->where('jenis','penjamin')->first();
                if ($oldPenjamin) {
                    Pekerjaan_referensi::where('referensi_id',$oldPenjamin->id)->delete();
                    $oldPenjamin->delete();
                }
            }
    
          /*
            =========================================
            SAUDARA
            edit / tambah / hapus
            =========================================
            */

            $submittedIds = [];
            // dd($request->saudara);
            if (!empty($request->saudara)) {
                foreach ($request->saudara as $index => $item) {
                    /*
                    skip jika nama kosong
                    */
                    if (empty($item['nama'])) {
                        continue;
                    }
                    /*
                    ===========================
                    JIKA DATA LAMA → UPDATE
                    ===========================
                    */
                    if (!empty($item['id'])) {
                        $saudara = Referensi::where('id',$item['id'])->where('pengajuan_id',$pengajuan->id)
                                            ->where('jenis','saudara')->first();
                        if ($saudara) {
                            $saudara->update([
                                'urutan' => $index + 1,
                                'nama' => $item['nama'],
                                'tempat_lahir' =>$item['tempat_lahir'] ?? null,
                                'tgl_lahir' =>$item['tgl_lahir'] ?? null,
                                'hubungan' =>$item['hubungan'] ?? null,
                                'no_hp' =>$item['no_hp'] ?? null,
                                'alamat' =>$item['alamat'] ?? null,
                            ]);
                            /*
                            simpan id yg diproses
                            */
                            $submittedIds[] = $saudara->id;
                        }
                    }

                    /*
                    ===========================
                    DATA BARU → CREATE
                    ===========================
                    */
                    else {
                        $saudara = Referensi::create([
                            'pengajuan_id' =>$pengajuan->id,
                            'jenis' =>'saudara',
                            'urutan' =>$index + 1,
                            'nama' =>$item['nama'],
                            'tempat_lahir' =>$item['tempat_lahir'] ?? null,
                            'tgl_lahir' =>$item['tgl_lahir'] ?? null,
                            'hubungan' =>$item['hubungan'] ?? null,
                            'no_hp' =>$item['no_hp'] ?? null,
                            'alamat' =>$item['alamat'] ?? null,
                        ]);
                        /*
                        simpan id baru
                        */
                        $submittedIds[] = $saudara->id;
                    }
                }
            }

            /*
            =========================================
            HAPUS SAUDARA YANG DIREMOVE USER
            =========================================
            */
            Referensi::where('pengajuan_id',$pengajuan->id)->where('jenis','saudara')
                    ->whereNotIn('id',$submittedIds)->delete();
    
            // UPDATE STEP
            $pengajuan->update(['current_step' => 4]);
    
            DB::commit();
            return redirect()->route('pengajuan.step4',$pengajuan->id);
    
        }
        catch (\Throwable $e) {
    
            DB::rollBack();
            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    public function step4(Pengajuan $pengajuan)
    {
        if ($pengajuan->status != 'draft') {
            abort(403);
        }

        $kategori = $pengajuan->kategori_nasabah;
    
        $docs = config("pengajuan.documents.$kategori", []);
        if(empty($docs)){
            abort(500,'Config dokumen tidak ditemukan');
        }
    
        $optionalDocs = config('pengajuan.documents.optional', []);
        $uploaded = $pengajuan->dokumenPengajuans()->get()->keyBy('jenis_dokumen');
    
        return view('pengajuans.step4',compact('pengajuan','docs','optionalDocs','uploaded'));
    }

    public function storeStep4(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'documents.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $kategori = $pengajuan->kategori_nasabah;
            $docs = config("pengajuan.documents.$kategori");
            // ambil existing docs sekali saja
            $existingDocs = $pengajuan->dokumenPengajuans->keyBy('jenis_dokumen');

            // VALIDASI REQUIRED DOCS
            foreach ($docs['required'] as $doc) {
                $exists = isset($existingDocs[$doc['code']]);
                if (
                    !$request->hasFile("documents.{$doc['code']}")
                    && !$exists
                ) {
                    return back()
                        ->withErrors([
                            'error' => "{$doc['label']} wajib diupload"
                        ])
                        ->withInput();
                }
            }

            // VALIDASI JAMINAN
            $hasBpkb = $request->hasFile('documents.bpkb');
            $hasSurat = $request->hasFile('documents.surat_tanah');

            $existingBpkb = isset($existingDocs['bpkb']);
            $existingSurat = isset($existingDocs['surat_tanah']);

            if (!$hasBpkb&&!$hasSurat&&!$existingBpkb&&!$existingSurat) {
                return back()
                    ->withErrors(['error' => 'Upload minimal BPKB atau Surat Tanah'])->withInput();
            }

            // UPLOAD FILES
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $jenis => $file) {
                    $existing = $existingDocs[$jenis] ?? null;

                    // hapus file lama
                    if ($existing) {Storage::disk('public')->delete($existing->file_path);}

                    // custom file name
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $jenis. '_'. $pengajuan->id. '_'. now()->format('YmdHis'). '.'. $extension;
                    $folder = "pengajuan/{$pengajuan->id}";
                    $path = $file->storeAs($folder,$fileName,'public');

                    // update/create
                    Dokumen_pengajuan::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'jenis_dokumen' => $jenis
                        ],
                        [
                            'nama_file' => $fileName,
                            'file_path' => $path,
                            'file_size' => $file->getSize(),

                            // upload ulang = reset review
                            'status' => 'pending',
                            'catatan' => null,
                            'uploaded_by' => auth()->id()
                        ]
                    );
                }
            }

            // update current step
            
            if ($pengajuan->current_step < 5) {
                $pengajuan->update(['current_step' => 5,'documents_completed' => true]);
            }

            DB::commit();
            return redirect()->route('pengajuan.review', $pengajuan->id)->with('success', 'Dokumen berhasil disimpan');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    public function review(Pengajuan $pengajuan)
    {

        if (!$pengajuan->documents_completed) {
            return redirect()->route('pengajuan.step4',$pengajuan->id)
                ->with('warning','Dokumen belum lengkap');
        }

        $pengajuan->load(['nasabah','nasabah.pekerjaan','referensis','referensis.pekerjaan','dokumenPengajuans','marketing','cabang',]);

        // ambil pasangan
        $pasangan = $pengajuan->referensis->where('jenis','pasangan')->first();
        
        // ambil penjamin
        $penjamin = $pengajuan->referensis->where('jenis','penjamin')->first();

        // ambil saudara (collection)
        $saudaras = $pengajuan->referensis->where('jenis','saudara');

        return view('pengajuans.review',compact('pengajuan','pasangan','penjamin','saudaras'));
    }

    public function analisa(Pengajuan $pengajuan)
    {
        $pengajuan->load('analisa');
        $analisa = $pengajuan->analisa;
        return view('pengajuans.analisa',compact('pengajuan','analisa'));
    }


    public function storeAnalisa(Request $request,Pengajuan $pengajuan)
    {
        $validated = $request->validate([
            'harga_kredit' => 'nullable',
            'kewajiban_angsuran' => 'nullable',
            'status_pemohon' => 'nullable',
            'status_tempat_tinggal' => 'nullable',
            'data_pemohon_lengkap' => 'nullable',
            'ktp_pemohon_valid' => 'nullable',
            'ktp_pasangan_valid' => 'nullable',
            'kk_valid' => 'nullable',
            'perbaikan_plafon' => 'nullable'
        ]);
    
        AnalisaPengajuan::updateOrCreate(
            [
                'pengajuan_id' => $pengajuan->id
            ],
            [
                'harga_kredit'          => $request->harga_kredit,
                'kewajiban_angsuran'    => $request->kewajiban_angsuran,
                'status_pemohon'        => $request->status_pemohon,
                'status_tempat_tinggal' => $request->status_tempat_tinggal,
                'data_pemohon_lengkap'  => $request->data_pemohon_lengkap,
                'ktp_pemohon_valid'     => $request->ktp_pemohon_valid,
                'ktp_pasangan_valid'    => $request->ktp_pasangan_valid,
                'kk_valid'              => $request->kk_valid,
                'perbaikan_plafon'      => $request->perbaikan_plafon,
                'created_by'            => auth()->id()
            ]
        );
    
        return redirect()->route('pengajuan.jaminan',$pengajuan->id);
    }

    public function jaminan(Pengajuan $pengajuan)
    {
        $pengajuan->load('jaminans');
        $jaminans = $pengajuan->jaminans;
        return view('pengajuans.jaminan',compact('pengajuan','jaminans'));
    }

    public function storeJaminan(Request $request,Pengajuan $pengajuan)
    {
        DB::beginTransaction();
        try {
            $existingIds = JaminanPengajuan::where('pengajuan_id',$pengajuan->id)->pluck('id')->toArray();
    
            $submittedIds = [];
            if (!empty($request->jaminan)) {
                foreach ($request->jaminan as $item) {
                    if (empty($item['nama_jaminan'])) {
                        continue;
                    }
    
                    // edit existing
                    if (!empty($item['id'])) {
                        $jaminan = JaminanPengajuan::find($item['id']);
                        if ($jaminan) {
                            $jaminan->update([
                                'jenis_jaminan' => $item['jenis_jaminan'],
                                'nama_jaminan'  => $item['nama_jaminan'],
                                'detail_jaminan'=> $item['detail_jaminan'],
                                'nilai_taksiran'=> $item['nilai_taksiran']
                            ]);
    
                            $submittedIds[] = $jaminan->id;
                        }
                    }
                    else {
                        $new =
                            JaminanPengajuan::create([
                                'pengajuan_id'      => $pengajuan->id,
                                'jenis_jaminan'     => $item['jenis_jaminan'],
                                'nama_jaminan'      => $item['nama_jaminan'],
                                'detail_jaminan'    => $item['detail_jaminan'],
                                'nilai_taksiran'    => $item['nilai_taksiran']
                            ]);
    
                        $submittedIds[] = $new->id;
                    }
                }
            }
    
            // hapus yang tidak dikirim
            $deletedIds = array_diff($existingIds,$submittedIds);
            JaminanPengajuan::whereIn('id',$deletedIds)->delete();
            DB::commit();
    
            return redirect()->route('pengajuan.kapital',$pengajuan->id);
        }
    
        catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function kapital(Pengajuan $pengajuan)
    {
        $pengajuan->load('kapital');
        $kapital = $pengajuan->kapital;
    
        return view('pengajuans.kapital',compact('pengajuan','kapital'));
    }

    public function storeKapital(Request $request,Pengajuan $pengajuan)
    {
        $validated = $request->validate([
            'omzet_harian' => 'nullable|integer',
            'laba_harian' => 'nullable|integer',
            'pendapatan_lain' => 'nullable|integer',
            'pendapatan_pasangan' => 'nullable|integer',
            'biaya_rumah_tangga' => 'nullable|integer',
            'biaya_motor' => 'nullable|integer',
            'biaya_koperasi' => 'nullable|integer',
            'angsuran_lain' => 'nullable|integer',
            'biaya_kontrak_rumah' => 'nullable|integer',
            'biaya_tempat_usaha' => 'nullable|integer'
        ]);
    
        $totalPengeluaran =
            ($request->biaya_rumah_tangga ?? 0)
            + ($request->biaya_motor ?? 0)
            + ($request->biaya_koperasi ?? 0)
            + ($request->angsuran_lain ?? 0)
            + ($request->biaya_kontrak_rumah ?? 0)
            + ($request->biaya_tempat_usaha ?? 0);
    
        $totalPendapatan =
            ($request->laba_harian ?? 0)
            + ($request->pendapatan_lain ?? 0)
            + ($request->pendapatan_pasangan ?? 0);
    
        $sisaPendapatan = $totalPendapatan - $totalPengeluaran;
    
        KapitalPengajuan::updateOrCreate(
            [
                'pengajuan_id' => $pengajuan->id
            ],
            [
                ...$validated,
                'total_pengeluaran'     => $totalPengeluaran,
                'sisa_pendapatan'       => $sisaPendapatan,
                'created_by'            => auth()->id()
            ]
        );
    
        return redirect()->route('pengajuan.surveyNotes',$pengajuan->id);
    }
    
}
