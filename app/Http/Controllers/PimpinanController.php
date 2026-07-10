<?php

namespace App\Http\Controllers;

use App\Models\ApprovalPengajuan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PimpinanController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        if (!$karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        $query = Pengajuan::query();

        if ($user->hasRole('kacab')) {
            $query->where('cabang_id', $karyawan->cabang_id);
        }

        $menungguReview = (clone $query)->where('status', 'menunggu_pimpinan')->count();

        $didisposisi = (clone $query)->whereIn('status', ['didisposisi_spvmarketing','didisposisi_surveyor',])->count();

        $ditolak = (clone $query)->where('status', 'ditolak_pimpinan')->count();

        $pengajuanTerbaru = (clone $query)->where('status', 'menunggu_pimpinan')
        ->with(['nasabah','marketing.user','cabang',])->latest()->take(10)->get();

        return view('dashboard.pimpinan', compact('menungguReview','didisposisi','ditolak','pengajuanTerbaru'));
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;
    
        $query = Pengajuan::with(['nasabah','marketing.user','cabang',]);
    
        // Kepala Cabang hanya melihat cabangnya
        if ($user->hasRole('kacab')) {
            $query->where('cabang_id', $karyawan->cabang_id);
        }
    
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('nasabah', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // default hanya menampilkan yang menunggu review
            $query->where('status', 'menunggu_pimpinan');
        }
    
        $pengajuans = $query->latest()->paginate(15)->withQueryString();
    
        return view('pimpinan.index', compact('pengajuans'));
    }

    public function show(Pengajuan $pengajuan)
    {
        
        $user = auth()->user();
        if ($user->hasRole('kacab')) {
            if ($pengajuan->cabang_id != $user->karyawan->cabang_id) {
                abort(403);
            }
        }
        /* 
        if ($pengajuan->status != 'menunggu_pimpinan') {
            return redirect()
                ->route('pimpinan.index')
                ->with('error','Pengajuan ini sudah tidak menunggu persetujuan pimpinan.');
        } */
    
        $karyawan = $user->karyawan;
        // Kepala Cabang hanya boleh melihat pengajuan cabangnya
        if ($user->hasRole('kacab')) {
            if ($pengajuan->cabang_id != $karyawan->cabang_id) {
                abort(403,'Anda tidak berhak melihat pengajuan ini.');
            }
        }
    
        $pengajuan->load(['nasabah','nasabah.pekerjaan','referensis','referensis.pekerjaan','dokumenPengajuans',
        'marketing.user','cabang','analisa','jaminans','kapital','approvals.user',]);
    
        $referensis = $pengajuan->referensis;
        $pasangan = $referensis->firstWhere('jenis','pasangan');
        $penjamin = $referensis->firstWhere('jenis','penjamin');
        $saudaras = $referensis->where('jenis','saudara');
    
        return view('pengajuans.review-final',[
            'pengajuan' => $pengajuan,
            'pasangan'  => $pasangan,
            'penjamin'  => $penjamin,
            'saudaras'  => $saudaras,
            // inilah bedanya dengan Marketing
            'mode'      => 'approval',
        ]);
    
    }

    public function submit(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->status != 'menunggu_pimpinan') {
            abort(403);
        }

        $validated = $request->validate([
            'aksi'      =>'required|in:tolak,survey',
            'catatan'   => 'nullable|string|max:5000',
        ]);

        DB::transaction(function () use ($pengajuan, $validated) {
            $statusLama = $pengajuan->status;
            switch ($validated['aksi']) {
                case 'tolak':
                    $statusBaru='ditolak_pimpinan';
                    break;
                case 'survey':
                    $statusBaru='menunggu_survey';
                    break;
            }

            $pengajuan->update(['status' => $statusBaru,]);

            ApprovalPengajuan::create([
                'pengajuan_id'      => $pengajuan->id,
                'user_id'           => auth()->id(),
                'role_name'         => auth()->user()->getRoleNames()->first(),
                'aksi'              => $validated['aksi'],
                'status_sebelumnya' => $statusLama,
                'status_sesudahnya' => $statusBaru,
                'catatan'           => $validated['catatan'],
            ]);

        });

        return redirect()->route('pimpinan.dashboard')->with('success', 'Keputusan berhasil disimpan.');
    }

    /**
     * Riwayat approval
     */
    public function history(Pengajuan $pengajuan)
    {
    }
}
