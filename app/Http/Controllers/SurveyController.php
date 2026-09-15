<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Survey;
use App\Models\SurveyBerkas;
use App\Models\SurveyDokumentasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\SurveyMediaService;

class SurveyController extends Controller
{
    public function dashboard()
    {

    }

    public function index()
    {
        $user = auth()->user();

        $karyawan = $user->karyawan;

        if (!$karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        if (!$karyawan->cabang_id) {
            abort(403, 'Cabang pengguna belum ditentukan.');
        }

        $cabangId = $karyawan->cabang_id;


        /*
        |--------------------------------------------------------------------------
        | SPV SURVEYOR
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('spvsurveyor')) {

            /*
            |--------------------------------------------------------------------------
            | PENGAJUAN YANG BELUM DIBUATKAN SURVEY
            |--------------------------------------------------------------------------
            */

            $pengajuans = Pengajuan::with([
                    'nasabah',
                    'marketing.user',
                    'cabang'
                ])
                ->where('cabang_id', $cabangId)
                ->where('status', 'menunggu_survey')
                ->latest()
                ->paginate(
                    10,
                    ['*'],
                    'pengajuan'
                );


            /*
            |--------------------------------------------------------------------------
            | SURVEY YANG DITUGASKAN KEPADA SPV INI
            |--------------------------------------------------------------------------
            */

            $surveySaya = Survey::with([
                    'pengajuan.nasabah',
                    'pengajuan.marketing.user',
                    'pengajuan.cabang'
                ])
                ->where('assigned_to', $user->id)
                ->whereHas('pengajuan', function ($query) use ($cabangId) {
                    $query->where('cabang_id', $cabangId);
                })
                ->whereIn(
                    'status',
                    [
                        'waiting',
                        'accepted',
                        'progress',
                        'revision'
                    ]
                )
                ->latest()
                ->paginate(
                    10,
                    ['*'],
                    'surveySaya'
                );


            /*
            |--------------------------------------------------------------------------
            | HASIL SURVEY YANG SUDAH DIKIRIM SURVEYOR
            |--------------------------------------------------------------------------
            */

            $surveys = Survey::with([
                    'pengajuan.nasabah',
                    'pengajuan.marketing.user',
                    'pengajuan.cabang',
                    'assignedTo'
                ])
                ->whereHas('pengajuan', function ($query) use ($cabangId) {
                    $query->where('cabang_id', $cabangId);
                })
                ->where('status', 'submitted')
                ->latest()
                ->paginate(
                    10,
                    ['*'],
                    'survey'
                );


            return view(
                'survey.index',
                compact(
                    'pengajuans',
                    'surveySaya',
                    'surveys'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SURVEYOR
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('surveyor')) {

            $surveys = Survey::with([
                    'pengajuan.nasabah',
                    'pengajuan.marketing.user',
                    'pengajuan.cabang'
                ])
                ->where('assigned_to', $user->id)
                ->whereHas('pengajuan', function ($query) use ($cabangId) {
                    $query->where('cabang_id', $cabangId);
                })
                ->latest()
                ->paginate(15);


            return view(
                'survey.index',
                compact('surveys')
            );
        }


        abort(403);
    }

    public function create(Pengajuan $pengajuan)
    {
        /*
        |--------------------------------------------------------------------------
        | HANYA PENGAJUAN MENUNGGU SURVEY
        |--------------------------------------------------------------------------
        */

        if ($pengajuan->status != 'menunggu_survey') {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DATA PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengajuan->load(['nasabah','nasabah.pekerjaanNasabah','referensis','referensis.pekerjaan','marketing','cabang',
            'analisa','jaminanPengajuans','jaminanPengajuans','jaminanPengajuans.dokumenJaminans','dokumenPayrolls','kapital',]);


        /*
        |--------------------------------------------------------------------------
        | REFERENSI
        |--------------------------------------------------------------------------
        */

        $referensis = $pengajuan->referensis;

        $pasangan = $referensis->firstWhere('jenis', 'pasangan');

        $penjamin = $referensis->firstWhere('jenis', 'penjamin');

        $saudaras = $referensis
            ->where('jenis', 'saudara')
            ->sortBy('urutan')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | DAFTAR SURVEYOR
        |--------------------------------------------------------------------------
        */

        $surveyors = User::role('surveyor')
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | HAK PENUGASAN
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();

        /*
        | SPV Surveyor dan Surveyor boleh
        | mengambil survey untuk dirinya sendiri
        */

        $canSurveySelf = $user->hasAnyRole([
            'spvsurveyor',
            'surveyor',
        ]);


        /*
        | Untuk sementara SPV Surveyor dan Surveyor
        | sama-sama boleh menugaskan surveyor lain.
        */

        $canAssignSurveyor = $user->hasAnyRole([
            'spvsurveyor',
            'surveyor',
        ]);


        return view('survey.create', [

            'pengajuan' => $pengajuan,

            'pasangan' => $pasangan,

            'penjamin' => $penjamin,

            'saudaras' => $saudaras,

            'surveyors' => $surveyors,

            'canSurveySelf' => $canSurveySelf,

            'canAssignSurveyor' => $canAssignSurveyor,

        ]);
    }

public function store(Request $request, Pengajuan $pengajuan)
{
    /*
    |--------------------------------------------------------------------------
    | PENGAJUAN HARUS MENUNGGU SURVEY
    |--------------------------------------------------------------------------
    */

    if ($pengajuan->status != 'menunggu_survey') {
        abort(403);
    }


    $user = auth()->user();


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate([

        'jenis' => [
            'required',
            'in:sendiri,assign',
        ],

        'surveyor_id' => [
            'nullable',
            'required_if:jenis,assign',
            'exists:users,id',
        ],

    ]);


    /*
    |--------------------------------------------------------------------------
    | SURVEY SENDIRI
    |--------------------------------------------------------------------------
    |
    | Surveyor dan SPV Surveyor boleh mengambil survey sendiri.
    |
    */

    if ($validated['jenis'] === 'sendiri') {

        if (
            !$user->hasAnyRole([
                'surveyor',
                'spvsurveyor',
            ])
        ) {

            abort(
                403,
                'Anda tidak diperbolehkan mengambil survey sendiri.'
            );
        }


        $assignedTo = $user->id;

        $status = 'accepted';

        $acceptedAt = now();
    }


    /*
    |--------------------------------------------------------------------------
    | ASSIGN SURVEYOR
    |--------------------------------------------------------------------------
    |
    | Hanya SPV Surveyor yang boleh menugaskan
    | survey kepada surveyor lain.
    |
    */

    else {

        if (
            !$user->hasRole('spvsurveyor')
        ) {

            abort(
                403,
                'Anda tidak diperbolehkan menugaskan surveyor.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SURVEYOR YANG DIPILIH HARUS ROLE SURVEYOR
        |--------------------------------------------------------------------------
        */

        $surveyor = User::role('surveyor')
            ->where(
                'id',
                $validated['surveyor_id']
            )
            ->first();


        if (!$surveyor) {

            abort(
                403,
                'User yang dipilih bukan surveyor.'
            );
        }


        $assignedTo = $surveyor->id;

        $status = 'waiting';

        $acceptedAt = null;
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN SURVEY
    |--------------------------------------------------------------------------
    */

    DB::transaction(function () use (
        $pengajuan,
        $user,
        $assignedTo,
        $status,
        $acceptedAt
    ) {

        Survey::create([

            'pengajuan_id' =>
                $pengajuan->id,

            'assigned_by' =>
                $user->id,

            'assigned_to' =>
                $assignedTo,

            'status' =>
                $status,

            'accepted_at' =>
                $acceptedAt,

        ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengajuan->update([

            'status' =>
                'survey_progress',

        ]);

    });


    /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

    return redirect()
        ->route('survey.index')
        ->with(
            'success',
            'Penugasan survey berhasil dibuat.'
        );
}
    

    public function accept(Survey $survey)
    {
        abort_if($survey->assigned_to != auth()->id(), 403);
    
        $survey->update(['status'=> 'accepted', 'accepted_at' => now(),]);
        return back()->with('success','Tugas survey berhasil diterima.');
    }

    public function start(Survey $survey)
    {
        abort_if($survey->assigned_to != auth()->id(), 403);
        abort_if($survey->status != 'accepted', 403);
    
        DB::transaction(function () use ($survey) {
            $survey->update([
                'status'     => 'progress',
                'started_at' => now(),
            ]);
            $survey->pengajuan->update([
                'status' => 'survey_progress',
            ]);
        });
        return redirect()->route('survey.berkas', $survey)->with('success', 'Survey berhasil dimulai.');
    }

   public function stepBerkas(Survey $survey)
    {
        abort_if(
            $survey->assigned_to != auth()->id(),
            403
        );

        abort_if(
            $survey->status != 'progress',
            403
        );


        $survey->load([
            'pengajuan.nasabah',
            'pengajuan.marketing',
            'pengajuan.cabang',

            'assignedTo',
            'assignedBy',

            'berkas',

            /*
            |--------------------------------------------------------------------------
            | DOKUMEN PENGAJUAN
            |--------------------------------------------------------------------------
            */

            'pengajuan.dokumenPengajuans',

            /*
            |--------------------------------------------------------------------------
            | DOKUMEN PAYROLL
            |--------------------------------------------------------------------------
            */

            'pengajuan.dokumenPayrolls',

            /*
            |--------------------------------------------------------------------------
            | JAMINAN + DOKUMEN JAMINAN
            |--------------------------------------------------------------------------
            */

            'pengajuan.jaminanPengajuans.dokumenJaminans',
        ]);

        return view('survey.berkas',compact('survey'));
    }

    public function storeStepBerkas(Request $request, Survey $survey)
    {
        // dd($request->all());
        abort_if($survey->assigned_to != auth()->id(),403);
        abort_if($survey->status != 'progress',403);
    
        $validated = $request->validate([
            'status_peminjam'          => 'required|in:baru,lama',
            'plafond_pinjaman_lama'    => 'nullable|numeric|min:0',
            'nama_pasangan_penjamin'   => 'nullable|string|max:150',
            'no_id_karyawan'           => 'nullable|string|max:100',
            'lama_bekerja'             => 'nullable|string|max:100',
            'no_telp_karyawan'         => 'nullable|string|max:30',
            'no_bpjs'                  => 'nullable|string|max:100',
            'nama_bank'                => 'nullable|string|max:100',
            'pin_atm'                  => 'nullable|string|max:20',
            'catatan_kekurangan'       => 'nullable|string|max:5000',
        ]);
    
        DB::transaction(function () use ($survey,$validated,$request){
    
            $data = [

                'ktp_debitur' => $request->boolean('ktp_debitur'),
                'kk_debitur' => $request->boolean('kk_debitur'),
            
                'status_peminjam' => $request->input('status_peminjam'),
                'plafond_pinjaman_lama' => $request->input('plafond_pinjaman_lama'),
            
                'ktp_penjamin' => $request->boolean('ktp_penjamin'),
                'kk_penjamin' => $request->boolean('kk_penjamin'),
            
                'nama_pasangan_penjamin' => $request->input('nama_pasangan_penjamin'),
            
                'ktp_pasangan_penjamin' => $request->boolean('ktp_pasangan_penjamin'),
                'kk_pasangan_penjamin' => $request->boolean('kk_pasangan_penjamin'),
            
                'slip_gaji' => $request->boolean('slip_gaji'),
                'no_id_karyawan' => $request->input('no_id_karyawan'),
                'lama_bekerja' => $request->input('lama_bekerja'),
                'no_telp_karyawan' => $request->input('no_telp_karyawan'),
            
                'bpjs' => $request->boolean('bpjs'),
                'no_bpjs' => $request->input('no_bpjs'),
            
                'buku_tabungan' => $request->boolean('buku_tabungan'),
                'nama_bank' => $request->input('nama_bank'),
            
                'kartu_atm' => $request->boolean('kartu_atm'),
                'pin_atm' => $request->input('pin_atm'),
            
                'catatan_kekurangan' => $request->input('catatan_kekurangan'),
            
            ];

            SurveyBerkas::updateOrCreate(
    
                [
                    'survey_id'=>$survey->id
                ],
                $data
             
            );
        });
    
        return redirect()->route('survey.dokumentasi',$survey)->with('success','Pemeriksaan berkas berhasil disimpan.');
    
    }

/*     public function stepDokumentasi(Survey $survey)
    {
        abort_if($survey->assigned_to != auth()->id(),403);

        abort_if($survey->status!='progress',403);

        $survey->load(['pengajuan','pengajuan.nasabah','pengajuan.jaminanPengajuans','dokumentasis',]);

        return view('survey.dokumentasi',compact('survey'));
    } */

    public function stepDokumentasi(Survey $survey)
    {
        abort_if($survey->assigned_to != auth()->id(), 403);
        abort_if($survey->status != 'progress', 403);

        $survey->load(['pengajuan','pengajuan.nasabah','pengajuan.jaminanPengajuans','dokumentasis',]);
        $dokumentasi = $survey->dokumentasis;

        // Dokumentasi Rumah
        $rumahDepan = $dokumentasi->where('kategori', 'rumah')->where('posisi', 'depan')->isNotEmpty();
        $rumahDalam = $dokumentasi->where('kategori', 'rumah')->where('posisi', 'dalam')->isNotEmpty();
        $rumahSamping = $dokumentasi->where('kategori', 'rumah')->where('posisi', 'samping')->isNotEmpty();

        // Dokumentasi Usaha
        $usaha = $dokumentasi->where('kategori', 'usaha')->isNotEmpty();

        // Dokumentasi per Jaminan
        $statusJaminan = [];

        foreach ($survey->pengajuan->jaminanPengajuans as $jaminan) {
            $statusJaminan[$jaminan->id] = [
                'depan' => $dokumentasi->where('kategori', 'jaminan')->where('jaminan_pengajuan_id', $jaminan->id)->where('posisi', 'depan')->isNotEmpty(),
                'samping' => $dokumentasi->where('kategori', 'jaminan')->where('jaminan_pengajuan_id', $jaminan->id)->where('posisi', 'samping')->isNotEmpty(),
                'belakang' => $dokumentasi->where('kategori', 'jaminan')->where('jaminan_pengajuan_id', $jaminan->id)->where('posisi', 'belakang')->isNotEmpty(),
                'dalam' => $dokumentasi->where('kategori', 'jaminan')->where('jaminan_pengajuan_id', $jaminan->id)->where('posisi', 'dalam')->isNotEmpty(),
                'video' => $dokumentasi->where('kategori', 'video')->where('jaminan_pengajuan_id', $jaminan->id)->isNotEmpty(),
            ];
        }
        return view('survey.dokumentasi', compact('survey','rumahDepan','rumahDalam','rumahSamping','usaha','statusJaminan'));
    }

  public function checkDokumentasi(Survey $survey)
    {
        /*
        |--------------------------------------------------------------------------
        | CEK HAK AKSES
        |--------------------------------------------------------------------------
        */

        abort_if(
            $survey->assigned_to != auth()->id(),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | SURVEY HARUS MASIH PROGRESS
        |--------------------------------------------------------------------------
        */

        abort_if(
            $survey->status != 'progress',
            403
        );


        /*
        |--------------------------------------------------------------------------
        | DOKUMENTASI OPTIONAL
        |--------------------------------------------------------------------------
        |
        | Tidak ada lagi validasi:
        |
        | - foto rumah
        | - foto tempat usaha
        | - foto jaminan
        | - video jaminan
        |
        | Semua dokumentasi bersifat optional.
        |
        */


        return redirect()
            ->route(
                'survey.review',
                $survey
            );
    }

    public function uploadDokumentasi(Request $request, Survey $survey, SurveyMediaService $mediaService)
    {
        abort_if($survey->assigned_to != auth()->id(),403);
        abort_if($survey->status!='progress',403);
        $request->validate([
            'kategori'=>'required',
            'files'=>'required',
            'files.*'=>'file|max:51200',
            'posisi'=>'nullable',
            'jaminan_pengajuan_id'=>'nullable|exists:jaminan_pengajuans,id'
        ]);
    
        DB::transaction(function() use($request,$survey){
            foreach($request->file('files') as $file){
                $path=$file->store("survey/".$survey->id,"public");
    
                SurveyDokumentasi::create([
                    'survey_id'=>$survey->id,
                    'jaminan_pengajuan_id'=>$request->jaminan_pengajuan_id,
                    'kategori'=>$request->kategori,
                    'posisi'=>$request->posisi,
                    'file'=>$path,
                ]);
            }
        });
    
        return response()->json([
            'success'=>true,
            'message'=>'Upload berhasil.'
        ]);
    }

    public function review(Survey $survey)
    {
        $user = auth()->user();
        if ($user->hasRole('surveyor')) {
            abort_if($survey->assigned_to != $user->id, 403);
            abort_if(!in_array($survey->status, ['progress', 'revision']),403);
            $mode = 'surveyor';
        }
        elseif ($user->hasRole('spvsurveyor')) {
            // SPV mengerjakan survey sendiri
            if ($survey->assigned_to == $user->id) {
                abort_if(!in_array($survey->status, ['progress','revision']),403);
                $mode = 'spv_self';
            }
            // SPV Review hasil Surveyor
            else {
                abort_if($survey->status != 'submitted',403);
                $mode = 'spv_review';
            }
        }
        else {
            abort(403);
        }
        $survey->load(['pengajuan','pengajuan.nasabah','pengajuan.nasabah.pekerjaanNasabah','pengajuan.marketing',
            'pengajuan.cabang','pengajuan.jaminanPengajuans','pengajuan.referensis','berkas','dokumentasis','assignedTo']);
    
        $rumah = $survey->dokumentasis->where('kategori', 'rumah');
        $usaha = $survey->dokumentasis->where('kategori', 'usaha');
        $jaminan = $survey->dokumentasis->where('kategori', 'jaminan');
        $videos = $survey->dokumentasis->where('kategori', 'video');
    
        return view('survey.review',compact('survey','rumah','usaha','jaminan','videos','mode'));
    }

    public function submit(Request $request, Survey $survey)
    {
        abort_if($survey->assigned_to != auth()->id(),403);
    
        abort_if($survey->status!='progress',403);
    
        $request->validate([
            'confirm_submit'=>'accepted'
        ]);
    
        DB::transaction(function() use($survey){
    
            if(auth()->user()->hasRole('spvsurveyor')){
    
                // Langsung ke pimpinan
    
                $survey->update([
                    'status'=>'submitted',
                    'submitted_at'=>now(),
                    'reviewed_at'=>now(),
                    'reviewed_by'=>auth()->id(),
                ]);
    
                $survey->pengajuan->update([
                    'status'=>'menunggu_keputusan'
                ]);
    
            }else{
                // Surveyor biasa
                $survey->update([
                    'status'=>'submitted',
                    'submitted_at'=>now(),
                ]);
    
            }
    
        });
    
        return redirect()->route('survey.index')->with('success',auth()->user()->hasRole('spvsurveyor')
                ? 'Survey berhasil dikirim ke Pimpinan.'
                : 'Survey berhasil dikirim ke SPV Survey.'
            );
    }
    
    public function reviewSpv(Survey $survey)
    {
        abort_if(!auth()->user()->hasRole('spvsurveyor'), 403);

        abort_if($survey->status != 'submitted', 403);

        $survey->load(['pengajuan','pengajuan.nasabah','pengajuan.nasabah.pekerjaanNasabah','pengajuan.marketing','pengajuan.cabang',
            'pengajuan.jaminanPengajuans','pengajuan.referensis','berkas','dokumentasis','assignedTo',]);

        $rumah   = $survey->dokumentasis->where('kategori', 'rumah');
        $usaha   = $survey->dokumentasis->where('kategori', 'usaha');
        $jaminan = $survey->dokumentasis->where('kategori', 'jaminan');
        $videos  = $survey->dokumentasis->where('kategori', 'video');

        $mode = 'spv';

        return view('survey.review', compact('survey','rumah','usaha','jaminan','videos','mode'));
    }

    public function submitReviewSpv(Request $request, Survey $survey)
    {
        abort_if(!auth()->user()->hasRole('spvsurveyor'), 403);

        abort_if($survey->status != 'submitted', 403);

        $validated = $request->validate([
            'aksi'     => 'required|in:revisi,approve',
            'catatan'  => 'nullable|string|max:5000',
        ]);

        DB::transaction(function () use ($survey, $validated) {

            if ($validated['aksi'] == 'revisi') {
                $survey->update([
                    'status' => 'progress',
                    'reviewed_at' => now(),
                    'reviewed_by' => auth()->id(),
                    'review_note' => $validated['catatan'],
                ]);
                $survey->pengajuan->update(['status' => 'survey_progress',]);
            } else {
                $survey->update([
                    'status' => 'reviewed',
                    'reviewed_at' => now(),
                    'reviewed_by' => auth()->id(),
                    'review_note' => $validated['catatan'],
                ]);

                $survey->pengajuan->update(['status' => 'menunggu_keputusan',]);
            }
        });

        $message = auth()->user()->hasRole('spvsurveyor')? 'Survey berhasil dikirim ke Pimpinan.': 'Survey berhasil dikirim ke SPV Survey.';
        return redirect()->route('survey.index')->with('success',$message);
    }

   

    public function storeStepDokumentasi(Request $request, Survey $survey)
    {
        abort_if($survey->assigned_to != auth()->id(), 403);
        abort_if($survey->status != 'progress', 403);
    
        $request->validate([
    
            'rumah.depan'   => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'rumah.dalam'   => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'rumah.samping' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    
            'usaha.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    
            'jaminan.*.depan'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'jaminan.*.samping'  => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'jaminan.*.dalam'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'jaminan.*.belakang' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    
            'video.*.*' => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:51200',
    
        ]);
    
        DB::transaction(function () use ($request, $survey) {
    
            //Hapus dokumentasi lama beserta file
            foreach ($survey->dokumentasis as $dokumentasi) {
                if (
                    $dokumentasi->file &&
                    Storage::disk('public')->exists($dokumentasi->file)
                ) {
                    Storage::disk('public')->delete($dokumentasi->file);
                }
            }
    
            $survey->dokumentasis()->delete();
    
            foreach (['depan', 'dalam', 'samping'] as $posisi) {
                if ($request->hasFile("rumah.$posisi")) {
                    $path = $request->file("rumah.$posisi")->store("survey/{$survey->id}/rumah", 'public');
                    $survey->dokumentasis()->create([
                        'kategori' => 'rumah',
                        'posisi' => $posisi,
                        'file' => $path,
                    ]);
                }
            }

            if ($request->hasFile('usaha')) {
                foreach ($request->file('usaha') as $file) {
                    // $path = $file->store("survey/{$survey->id}/usaha",'public');
                    $path = $mediaService->store($file,$survey->id);
                    $survey->dokumentasis()->create([
                        'kategori' => 'usaha',
                        'posisi' => 'usaha',
                        'file' => $path,
                    ]);
                }
            }
    
            //FOTO JAMINAN
            foreach ($request->file('jaminan', []) as $jaminanId => $files) {
                foreach (['depan', 'samping', 'dalam', 'belakang'] as $posisi) {
                    if (isset($files[$posisi])) {
                        $path = $files[$posisi]->store("survey/{$survey->id}/jaminan/{$jaminanId}",'public');
    
                        $survey->dokumentasis()->create([
                            'jaminan_pengajuan_id' => $jaminanId,
                            'kategori' => 'jaminan',
                            'posisi' => $posisi,
                            'file' => $path,
                        ]);
                    }
                }
            }
    
            //VIDEO JAMINAN
            foreach ($request->file('video', []) as $jaminanId => $videos) {
                foreach ($videos as $video) {
                    $path = $video->store("survey/{$survey->id}/video/{$jaminanId}",'public');
    
                    $survey->dokumentasis()->create([
                        'jaminan_pengajuan_id' => $jaminanId,
                        'kategori' => 'video',
                        'posisi' => 'video',
                        'file' => $path,
                    ]);
                }
            }
        });
    
        return redirect()->route('survey.review', $survey)->with('success', 'Dokumentasi survey berhasil disimpan.');
    }

    public function showPengajuan(Pengajuan $pengajuan)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | CEK SURVEYOR
        |--------------------------------------------------------------------------
        */

        if (!$user->hasAnyRole(['surveyor', 'spvsurveyor'])) {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | CEK APAKAH PENGAJUAN INI MEMILIKI SURVEY UNTUK USER
        |--------------------------------------------------------------------------
        */

        $survey = Survey::where('pengajuan_id', $pengajuan->id)
            ->where('assigned_to', $user->id)
            ->latest()
            ->first();

        if (!$survey) {
            abort(403, 'Anda tidak ditugaskan untuk survey pengajuan ini.');
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DATA PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $pengajuan->load(['nasabah','nasabah.pekerjaanNasabah','referensis','referensis.pekerjaan','dokumenPengajuans','marketing',
            'marketing.user','cabang','analisa','jaminanPengajuans','jaminanPengajuans.dokumenJaminans','dokumenPayrolls','kapital']);


        /*
        |--------------------------------------------------------------------------
        | REFERENSI
        |--------------------------------------------------------------------------
        */

        $referensis = $pengajuan->referensis;

        $pasangan = $referensis->firstWhere('jenis','pasangan');

        $penjamin = $referensis->firstWhere('jenis','penjamin');

        $saudaras = $referensis->where('jenis','saudara');

        return view('survey.pengajuan-show', [
            'pengajuan' => $pengajuan,
            'survey' => $survey,
            'pasangan' => $pasangan,
            'penjamin' => $penjamin,
            'saudaras' => $saudaras,
            'mode' => 'survey',
        ]);
    }    
}
