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

class SurveyController extends Controller
{
    public function dashboard()
    {

    }

    public function index()
    {
        $user = auth()->user();
    
        if ($user->hasRole('spvsurveyor')) {
    
            // Pengajuan yang belum dibuatkan survey
            $pengajuans = Pengajuan::with(['nasabah','marketing.user','cabang'])
                ->where('status', 'menunggu_survey')
                ->latest()
                ->paginate(10, ['*'], 'pengajuan');
    
            $surveySaya = Survey::with(['pengajuan.nasabah','pengajuan.marketing.user','pengajuan.cabang'])
            ->where('assigned_to',$user->id)->whereIn('status',['waiting','accepted','progress','revision'])
            ->latest()->paginate(10,['*'],'surveySaya');
            
            // Hasil survey yang sudah dikirim Surveyor
            $surveys = Survey::with(['pengajuan.nasabah','pengajuan.marketing.user','pengajuan.cabang','assignedTo'])
                ->where('status', 'submitted')
                ->latest()
                ->paginate(10, ['*'], 'survey');
    
            return view('survey.index', compact('pengajuans','surveySaya','surveys'));
        }
    
        if ($user->hasRole('surveyor')) {
    
            $surveys = Survey::with(['pengajuan.nasabah','pengajuan.marketing.user','pengajuan.cabang'])
                ->where('assigned_to', $user->id)
                ->latest()
                ->paginate(15);
    
            return view('survey.index', compact('surveys'));
        }
    
        abort(403);
    }

    public function create(Pengajuan $pengajuan)
    {
        if ($pengajuan->status != 'menunggu_survey') {
            abort(403);
        }
        $surveyors = User::role('surveyor')->orderBy('name')->get();
        return view('survey.create', compact('pengajuan','surveyors'));
    }

    public function store(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->status != 'menunggu_survey') {
            abort(403);
        }
    
        $validated = $request->validate([
            'jenis' => 'required|in:sendiri,assign',
            'surveyor_id' => 'nullable|required_if:jenis,assign|exists:users,id',
        ]);
    
        DB::transaction(function () use ($validated, $pengajuan) {
            if ($validated['jenis'] == 'sendiri') {
                Survey::create([
                    'pengajuan_id' => $pengajuan->id,
                    'assigned_by' => auth()->id(),
                    'assigned_to' => auth()->id(),
                    'status' => 'accepted',
                    'accepted_at' => now(),
                ]);
            } else {
                Survey::create([
                    'pengajuan_id' => $pengajuan->id,
                    'assigned_by' => auth()->id(),
                    'assigned_to' => $validated['surveyor_id'],
                    'status' => 'waiting',
                ]);
            }
            $pengajuan->update(['status' => 'survey_progress',]);
        });
    
        return redirect()->route('survey.index')->with('success', 'Penugasan survey berhasil dibuat.');
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
        abort_if($survey->assigned_to != auth()->id(), 403);

        abort_if($survey->status != 'progress', 403);

        $survey->load(['pengajuan.nasabah','pengajuan.marketing','pengajuan.cabang', 'assignedTo', 'assignedBy', 'berkas',]);

        return view('survey.berkas', compact('survey'));
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

    public function stepDokumentasi(Survey $survey)
    {
        abort_if($survey->assigned_to != auth()->id(),403);

        abort_if($survey->status!='progress',403);

        $survey->load(['pengajuan','pengajuan.nasabah','pengajuan.jaminans','dokumentasis',]);

        return view('survey.dokumentasi',compact('survey'));
    }

    public function checkDokumentasi(Survey $survey)
    {
        abort_if($survey->assigned_to != auth()->id(),403);
    
        abort_if($survey->status!='progress',403);
    
        /*
        |--------------------------------------------------------------------------
        | CEK FOTO RUMAH
        |--------------------------------------------------------------------------
        */
    
        $rumah = SurveyDokumentasi::where('survey_id',$survey->id)
                    ->where('kategori','rumah')
                    ->pluck('posisi')
                    ->toArray();
    
        foreach(['depan','dalam','samping'] as $posisi){
    
            if(!in_array($posisi,$rumah)){
    
                return back()->withErrors([
                    'dokumentasi'=>'Foto rumah belum lengkap.'
                ]);
    
            }
    
        }
    
        /*
        |--------------------------------------------------------------------------
        | CEK JAMINAN
        |--------------------------------------------------------------------------
        */
    
        foreach($survey->pengajuan->jaminans as $jaminan){
    
            $foto = SurveyDokumentasi::where('survey_id',$survey->id)
                        ->where('jaminan_pengajuan_id',$jaminan->id)
                        ->where('kategori','jaminan')
                        ->pluck('posisi')
                        ->toArray();
    
            foreach(['depan','samping','dalam','belakang'] as $posisi){
    
                if(!in_array($posisi,$foto)){
    
                    return back()->withErrors([
                        'dokumentasi'=>'Foto jaminan belum lengkap.'
                    ]);
    
                }
    
            }
    
            $video = SurveyDokumentasi::where('survey_id',$survey->id)
                        ->where('jaminan_pengajuan_id',$jaminan->id)
                        ->where('kategori','video')
                        ->exists();
    
            if(!$video){
    
                return back()->withErrors([
                    'dokumentasi'=>'Video jaminan belum diupload.'
                ]);
    
            }
    
        }
    
        return redirect()->route('survey.review',$survey);
    }

    public function uploadDokumentasi(Request $request, Survey $survey)
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
        $survey->load(['pengajuan','pengajuan.nasabah','pengajuan.nasabah.pekerjaan','pengajuan.marketing',
            'pengajuan.cabang','pengajuan.jaminans','pengajuan.referensis','berkas','dokumentasis','assignedTo']);
    
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

        $survey->load(['pengajuan','pengajuan.nasabah','pengajuan.nasabah.pekerjaan','pengajuan.marketing','pengajuan.cabang',
            'pengajuan.jaminans','pengajuan.referensis','berkas','dokumentasis','assignedTo',]);

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

                $survey->pengajuan->update(['status' => 'menunggu_pimpinan',]);
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
                    $path = $file->store("survey/{$survey->id}/usaha",'public');
    
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
}
