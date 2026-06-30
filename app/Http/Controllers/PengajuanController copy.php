<?php

namespace App\Http\Controllers;


use App\Http\Requests\PengajuanStep1Request;
use App\Http\Requests\PengajuanStep3Request;
use App\Http\Requests\PengajuanStep4Request;
use App\Models\Cabang;
use App\Models\Dokumen_pengajuan;
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
    
            // simpan kategori lama
            $oldKategori =$pengajuan->kategori_nasabah;
    
            $pengajuan->update([
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'nominal_pengajuan' => $request->nominal_pengajuan,
                'tenor' =>$request->tenor,
                'kategori_nasabah' =>$request->kategori_nasabah,
                'tujuan_pinjaman' =>$request->tujuan_pinjaman,
                'catatan' =>$request->catatan,
            ]);
    
            /*
            jika kategori berubah payroll → non_payroll
            nanti kita hapus dokumen yang tidak relevan
            skip dulu untuk sekarang
            */
    
            DB::commit();
            // balik ke step2    
            return redirect()->route('pengajuan.step2',$pengajuan->id)
                    ->with('success','Step 1 berhasil diupdate');
    
        }
        catch(\Throwable $e){
            DB::rollBack();
            return back()->withInput()->with('error',$e->getMessage());
        }
    }

  /*   public function step2(Pengajuan $pengajuan)
    {
        return view('pengajuans.step2', compact('pengajuan'));
    } */

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
    
            // NASABAH
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
    
            // PEKERJAAN
            'jenis_pekerjaan' => 'required',
            'penghasilan' => 'required|integer',
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
    
        DB::transaction(function () use ($validated, $pengajuan) {
    
            // SIMPAN NASABAH
            $nasabah = Nasabah::create([
                'pengajuan_id' => $pengajuan->id,
                'nama' => $validated['nama'],
                'nik' => $validated['nik'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tgl_lahir' => $validated['tgl_lahir'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'status_perkawinan' => $validated['status_perkawinan'],
                'jumlah_tanggungan' => $validated['jumlah_tanggungan'],
                'status_rumah' => $validated['status_rumah'],
                'lama_menetap_tahun' => $validated['lama_menetap_tahun'],
                'lama_menetap_bulan' => $validated['lama_menetap_bulan']
            ]);
    
            // SIMPAN PEKERJAAN
            Pekerjaan_nasabah::create([
                'nasabah_id' => $nasabah->id,
                'jenis_pekerjaan' => $validated['jenis_pekerjaan'],
                'penghasilan' => $validated['penghasilan'],
                'nama_usaha' => $validated['nama_usaha'],
                'jenis_usaha' => $validated['jenis_usaha'],
                'lama_usaha' => $validated['lama_usaha'],
                'jumlah_pegawai' => $validated['jumlah_pegawai'],
                'alamat_usaha' => $validated['alamat_usaha'],
                'telpon_usaha' => $validated['telpon_usaha'],
                'bangunan_usaha' => $validated['bangunan_usaha'],
                'status_tempat_usaha' => $validated['status_tempat_usaha'],
                'aktivitas_usaha' => $validated['aktivitas_usaha']
            ]);
        });
    
        return redirect()->route('pengajuan.step3', $pengajuan->id);
    }

    public function step3(Pengajuan $pengajuan)
    {
        return view('pengajuans.step3', compact('pengajuan'));
    }

    public function storeStep3(PengajuanStep3Request $request, Pengajuan $pengajuan)
    {
        DB::beginTransaction();

        try {
            // PASANGAN
            
            if (!empty($request->pasangan['nama'])) {

                $pasangan = Referensi::updateOrCreate(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'jenis' => 'pasangan'
                    ],
                    [
                        'nama' => $request->pasangan['nama'],
                        'tempat_lahir' => $request->pasangan['tempat_lahir'] ?? null,
                        'tgl_lahir' => $request->pasangan['tgl_lahir'] ?? null,
                    ]
                );

                // pekerjaan pasangan
                if (!empty($request->pasangan['pekerjaan'])) {

                    Pekerjaan_referensi::updateOrCreate(
                        [
                            'referensi_id' => $pasangan->id
                        ],
                        [
                            'penghasilan' => $request->pasangan['pekerjaan']['penghasilan'] ?? null,
                            'nama_usaha' => $request->pasangan['pekerjaan']['nama_usaha'] ?? null,
                            'jenis_usaha' => $request->pasangan['pekerjaan']['jenis_usaha'] ?? null,
                            'lama_usaha' => $request->pasangan['pekerjaan']['lama_usaha'] ?? null,
                            'jumlah_pegawai' => $request->pasangan['pekerjaan']['jumlah_pegawai'] ?? null,
                            'alamat_usaha' => $request->pasangan['pekerjaan']['alamat_usaha'] ?? null,
                        ]
                    );
                }
            }

            // PENJAMIN
            
            if (!empty($request->penjamin['nama'])) {

                $penjamin = Referensi::updateOrCreate(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'jenis' => 'penjamin'
                    ],
                    [
                        'nama' => $request->penjamin['nama'],
                        'tempat_lahir' => $request->penjamin['tempat_lahir'] ?? null,
                        'tgl_lahir' => $request->penjamin['tgl_lahir'] ?? null,
                        'hubungan' => $request->penjamin['hubungan'] ?? null,
                        'no_hp' => $request->penjamin['no_hp'] ?? null,
                        'alamat' => $request->penjamin['alamat'] ?? null,
                    ]
                );

                // pekerjaan penjamin
                if (!empty($request->penjamin['pekerjaan'])) {
                    Pekerjaan_referensi::updateOrCreate(
                        [
                            'referensi_id' => $penjamin->id
                        ],
                        [
                            'penghasilan' => $request->penjamin['pekerjaan']['penghasilan'] ?? null,
                            'nama_usaha' => $request->penjamin['pekerjaan']['nama_usaha'] ?? null,
                            'jenis_usaha' => $request->penjamin['pekerjaan']['jenis_usaha'] ?? null,
                            'lama_usaha' => $request->penjamin['pekerjaan']['lama_usaha'] ?? null,
                            'jumlah_pegawai' => $request->penjamin['pekerjaan']['jumlah_pegawai'] ?? null,
                            'alamat_usaha' => $request->penjamin['pekerjaan']['alamat_usaha'] ?? null,
                        ]
                    );
                }
            }

            // SAUDARA
            if (!empty($request->saudara)) {
                foreach ($request->saudara as $index => $item) {
                    if (empty($item['nama'])) {
                        continue;
                    }
                    Referensi::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'jenis' => 'saudara',
                            'urutan' => $index + 1
                        ],
                        [
                            'nama' => $item['nama'],
                            'tempat_lahir' => $item['tempat_lahir'] ?? null,
                            'tgl_lahir' => $item['tgl_lahir'] ?? null,
                            'hubungan' => $item['hubungan'] ?? null,
                            'no_hp' => $item['no_hp'] ?? null,
                            'alamat' => $item['alamat'] ?? null,
                        ]
                    );
                }
            }

            $pengajuan->update(['current_step' => 4]);

            DB::commit();

            return redirect()->route('pengajuan.step4', $pengajuan->id);

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

 /*   public function step4(Pengajuan $pengajuan)
    {
        $documents = ['ktp_pemohon','ktp_pasangan','kk','slip_gaji','npwp','bpkb','stnk','surat_usaha','foto_jaminan'];

        $uploaded = $pengajuan->Dokumen_pengajuans()->get()->keyBy('jenis_dokumen');

        return view('pengajuans.step4',compact('pengajuan','documents','uploaded')
        );
    } */
/* 
    public function storeStep4(PengajuanStep4Request $request,Pengajuan $pengajuan)
    {
        DB::transaction(function () use ($request,$pengajuan) {
    
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents')as $jenis => $file) {
                    // cek dokumen lama
                    
                    $existing = Dokumen_pengajuan::where('pengajuan_id',$pengajuan->id)
                    ->where('jenis_dokumen',$jenis)->first();
    
                    // hapus file lama
                    if ($existing) {
                        Storage::disk('public')->delete($existing->file_path);
                    }
    
                    // simpan file baru
                    $path = $file->store("pengajuan/{$pengajuan->id}",'public');
    
                    Dokumen_pengajuan::updateOrCreate(
                        [
                            'pengajuan_id' =>$pengajuan->id,
                            'jenis_dokumen' =>$jenis
                        ],
                        [
                            'nama_file' =>$file->getClientOriginalName(),
                            'file_path' =>$path,
                            'file_size' =>$file->getSize(),
                            // upload ulang = reset status
                            'status' => 'pending',
                            'catatan' => null,
                            'uploaded_by' =>auth()->id()
                        ]
                    );
                }
            }
        
            $pengajuan->update(['current_step' => 5]);
        });
    
        return redirect()->route('pengajuan.review',$pengajuan->id);
    } */

    public function step4(Pengajuan $pengajuan)
    {
        //ambil kategori dari tabel pengajuans
        $kategori = $pengajuan->kategori_nasabah;

        // ambil config sesuai kategori : payroll atau non_payroll
        $docs = config("pengajuan.documents.$kategori");

        // optional docs
        $optionalDocs = config('pengajuan.documents.optional');

        // dokumen yang sudah pernah upload
        $uploaded = $pengajuan->dokumenPengajuans()->get()->keyBy('jenis_dokumen');

        return view('pengajuans.step4',compact('pengajuan','docs','optionalDocs','uploaded'));
    }

    public function storeStep4(Request $request, Pengajuan $pengajuan)
    {
        DB::beginTransaction();

        try {

            $kategori = $pengajuan->kategori_nasabah;

            $docs = config("pengajuan.documents.$kategori");

            /*
            ===============================
            VALIDASI MANUAL REQUIRED DOCS
            ===============================
            */

            foreach ($docs['required'] as $doc) {

                $existing = Dokumen_pengajuan::where('pengajuan_id',$pengajuan->id)
                ->where('jenis_dokumen',$doc['code'])->exists();

                if (
                    !$request->hasFile("documents.{$doc['code']}")
                    &&
                    !$existing
                ) {
                    return back()
                        ->withErrors(['error' => "{$doc['label']} wajib diupload"]);
                }
            }


            /*
            ===============================
            VALIDASI BPKB ATAU SURAT TANAH
            ===============================
            */

            $hasBpkb = $request->hasFile('documents.bpkb');

            $hasSurat = $request->hasFile('documents.surat_tanah');

            $existingBpkb = Dokumen_pengajuan::where('pengajuan_id',$pengajuan->id)
            ->where('jenis_dokumen','bpkb')->exists();

            $existingSurat = Dokumen_pengajuan::where('pengajuan_id',$pengajuan->id)
            ->where('jenis_dokumen','surat_tanah')->exists();

            if (!$hasBpkb && !$hasSurat && !$existingBpkb && !$existingSurat) {
                return back()->withErrors(['error' =>'Upload minimal BPKB atau Surat Tanah']);
            }

            /*
            ===============================
            UPLOAD FILES
            ===============================
            */

            if ($request->hasFile('documents')) {

                foreach ($request->file('documents')as $jenis => $file) {
                    /*
                    cek file lama
                    */
                    $existing = Dokumen_pengajuan::where('pengajuan_id',$pengajuan->id)
                    ->where('jenis_dokumen',$jenis)->first();
                    /*
                    hapus lama
                    */
                    if ($existing) {
                        Storage::disk('public')->delete($existing->file_path);
                    }
                    /*
                    upload baru
                    */
                    // $path = $file->store("pengajuan/{$pengajuan->id}",'public');

                    /*
                    buat nama file custom
                    */

                    $extension = $file->getClientOriginalExtension();
                    $fileName = $jenis . '_' . $pengajuan->id . '.' . $extension;
                    
                    // folder: pengajuan/4/
                    $folder = "pengajuan/{$pengajuan->id}";

                    $path = $file->storeAs($folder,$fileName,'public');

                    /*
                    update/create
                    */

                    Dokumen_pengajuan::updateOrCreate(
                        [
                            'pengajuan_id' =>$pengajuan->id,
                            'jenis_dokumen' =>$jenis
                        ],
                        [
                            // 'nama_file' =>$file->getClientOriginalName(),
                            'nama_file' => $fileName,
                            'file_path' =>$path,
                            'file_size' =>$file->getSize(),
                            'status' => 'pending',
                            'catatan' => null,
                            'uploaded_by' => auth()->id()
                        ]
                    );
                }
            }

           /*
            next step
            */
            $pengajuan->update(['current_step' => 5]);

            DB::commit();

            return redirect()->route('pengajuan.review',$pengajuan->id);

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('error',$e->getMessage());
        }
    }

 /*    public function review(Pengajuan $pengajuan)
    {
        $pengajuan->load(['nasabah','nasabah.pekerjaan','referensis','referensis.pekerjaan','dokumenPengajuans','marketing','cabang',]);
    
        return view('pengajuans.review',compact('pengajuan'));
    } */

    public function review(Pengajuan $pengajuan)
    {
        $pengajuan->load(['nasabah','nasabah.pekerjaan','referensis','referensis.pekerjaan','dokumenPengajuans','marketing','cabang',]);

        // ambil pasangan
        $pasangan = $pengajuan->referensis->where('jenis','pasangan')->first();
        // ambil penjamin
        $penjamin = $pengajuan->referensis->where('jenis','penjamin')->first();
        // ambil saudara (collection)
        $saudaras = $pengajuan->referensis->where('jenis','saudara');
        return view('pengajuans.review',compact('pengajuan','pasangan','penjamin','saudaras'));
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
