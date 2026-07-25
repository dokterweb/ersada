<?php

namespace App\Http\Controllers;

use App\Models\ApprovalPengajuan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalSurveyController extends Controller
{
    public function index()
    {
        $pengajuans = Pengajuan::with(['nasabah','marketing','cabang','survey','survey.assignedTo',])
        ->whereHas('survey', function ($q) {
            $q->where('status', 'submitted');
        })->latest()->paginate(10);

        // dd($pengajuans->first()->toArray());
        return view('approval-survey.index',compact('pengajuans')
        );
    }

    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load(['nasabah','nasabah.pekerjaan','marketing.user','cabang','referensis','jaminans',
            'survey','survey.berkas','survey.dokumentasis','survey.assignedTo',]);

        $survey = $pengajuan->survey;

        abort_if(!$survey,404);

        $rumah      = $survey->dokumentasis->where('kategori','rumah');
        $usaha      = $survey->dokumentasis->where('kategori','usaha');
        $jaminan    = $survey->dokumentasis->where('kategori','jaminan');
        $videos     = $survey->dokumentasis->where('kategori','video');

        return view('approval-survey.show',compact('pengajuan','survey','rumah','usaha','jaminan','videos'));
    }

    public function store(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'keputusan' => 'required|in:setuju,revisi,tolak',
            'catatan'   => 'nullable|string|max:5000',
        ]);
    
        if (in_array($request->keputusan, ['revisi', 'tolak'])) {
            $request->validate([
                'catatan' => 'required|string|max:5000',
            ]);
        }
    
        $survey = $pengajuan->survey;
    
        abort_if(!$survey, 404, 'Survey tidak ditemukan.');
        abort_if($survey->status !== 'submitted', 403, 'Survey sudah diproses.');
    
        DB::transaction(function () use ($request, $pengajuan, $survey) {
    
            switch ($request->keputusan) {
    
                case 'setuju':
                    ApprovalPengajuan::create([
                        'pengajuan_id'       => $pengajuan->id,
                        'user_id'            => auth()->id(),
                        'role_name'          => auth()->user()->getRoleNames()->first(),
                        'aksi'               => 'approve_survey',
                        'status_sebelumnya'  => $pengajuan->status,
                        'status_sesudahnya'  => 'analisa',
                        'catatan'            => $request->catatan,
                    
                    ]);

                    $survey->update(['status' => 'approved']);
                    $pengajuan->update(['status' => 'disetujui']);
                    break;
    
                case 'revisi':
                    $survey->update(['status' => 'revision']);
                    $pengajuan->update(['status' => 'survey_revision']);
                    break;
    
                case 'tolak':
                    $survey->update(['status' => 'rejected']);
                    $pengajuan->update(['status' => 'ditolak']);
                    break;
            }
        });
    
        return redirect()->route('approvalSurvey.index')->with('success', 'Keputusan berhasil disimpan.');
    }
}
