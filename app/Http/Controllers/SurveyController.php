<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function dashboard()
    {

    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('spvsurveyor')) {
            $pengajuans = Pengajuan::with(['nasabah', 'marketing.user','cabang'])
                ->where('status', 'menunggu_survey')->latest()->paginate(15);

            return view('survey.index', compact('pengajuans'));
        }

        if ($user->hasRole('surveyor')) {
            $surveys = Survey::with(['pengajuan.nasabah','pengajuan.marketing','pengajuan.cabang'])
                ->where('assigned_to', $user->id)->latest()->paginate(15);

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

    }

    public function start(Survey $survey)
    {

    }
}
