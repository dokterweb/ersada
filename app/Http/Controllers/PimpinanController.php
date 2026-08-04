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
    
        $query = Pengajuan::with(['nasabah','marketing.user','cabang']);
    
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
            // Default tampilkan pekerjaan yang harus dikerjakan pimpinan
            $query->whereIn('status', [
                'menunggu_pimpinan',
                'menunggu_keputusan',
            ]);
        }
    
        $pengajuans = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();
    
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
    
        $karyawan = $user->karyawan;
        // Kepala Cabang hanya boleh melihat pengajuan cabangnya
        if ($user->hasRole('kacab')) {
            if ($pengajuan->cabang_id != $karyawan->cabang_id) {
                abort(403,'Anda tidak berhak melihat pengajuan ini.');
            }
        }
    
        $pengajuan->load(['nasabah','nasabah.pekerjaanNasabah','referensis','referensis.pekerjaan','dokumenPengajuans',
        'marketing.user','cabang','analisa','jaminanPengajuans','kapital','approvals.user',]);
    
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
        abort_unless(in_array($pengajuan->status, [
                'menunggu_pimpinan',
                'menunggu_keputusan',
            ]),403
        );

        //VALIDASI BERDASARKAN STATUS
        if ($pengajuan->status == 'menunggu_pimpinan') {
            $validated = $request->validate([
                'aksi'     => 'required|in:survey,tolak',
                'catatan'  => 'nullable|string|max:5000',
            ]);
        } else {
            $validated = $request->validate([
                'aksi'                => 'required|in:setujui,tolak',
                'catatan'             => 'nullable|string|max:5000',
                'plafond_disetujui'   => 'required',
                'tenor_disetujui'     => 'required|integer|min:1',
            ]);
        }

        DB::transaction(function () use ($pengajuan, $validated) {
            $statusLama = $pengajuan->status;
            $plafondDisetujui = null;
            // TAHAP 1 | MENUNGGU PIMPINAN
            if ($statusLama == 'menunggu_pimpinan') {
                switch ($validated['aksi']) {
                    case 'survey':
                        $statusBaru = 'menunggu_survey';
                        break;
                    case 'tolak':
                        $statusBaru = 'ditolak';
                        break;
                    default:
                        abort(422);
                }
                $pengajuan->update(['status' => $statusBaru,]);
            }

            // TAHAP 2 | MENUNGGU KEPUTUSAN
            else {
                $plafondDisetujui = (int) preg_replace('/[^0-9]/','',$validated['plafond_disetujui']);
                switch ($validated['aksi']) {
                    case 'setujui':
                        $statusBaru = 'disetujui';
                        break;
                    case 'tolak':
                        $statusBaru = 'ditolak';
                        break;
                    default:
                        abort(422);
                }
                $pengajuan->update([
                    'status'              => $statusBaru,
                    'plafond_disetujui'   => $plafondDisetujui,
                    'tenor_disetujui'     => $validated['tenor_disetujui'],
                ]);
            }

            //SIMPAN RIWAYAT APPROVAL
            ApprovalPengajuan::create([
                'pengajuan_id'      => $pengajuan->id,
                'user_id'           => auth()->id(),
                'role_name'         => auth()->user()->getRoleNames()->first(),
                'aksi'              => $validated['aksi'],
                'status_sebelumnya' => $statusLama,
                'status_sesudahnya' => $statusBaru,
                'catatan'           => $validated['catatan'] ?? null,
                'plafond_disetujui' => $plafondDisetujui,
                'tenor_disetujui'   => $validated['tenor_disetujui'] ?? null,
            ]);
        });

        return redirect()->route('pimpinan.dashboard')->with('success', 'Keputusan berhasil disimpan.');
    }

/* 
    public function submit(Request $request, Pengajuan $pengajuan)
    {
        if (!in_array($pengajuan->status, [
            'menunggu_pimpinan',
            'menunggu_keputusan'
        ])) {
            abort(403);
        }

        $validated = $request->validate([
            'aksi'              => 'required|in:tolak,survey,setujui',
            'catatan'           => 'nullable|string|max:5000',
            'plafond_disetujui' => ['nullable','required_if:aksi,setujui'],
            'tenor_disetujui'   => ['nullable','required_if:aksi,setujui','integer','min:1'],
        ]);

        DB::transaction(function () use ($pengajuan, $validated) {
            $statusLama = $pengajuan->status;
            switch ($validated['aksi']) {

                case 'survey':
                    $statusBaru = 'menunggu_survey';
                    break;
            
                case 'setujui':
                    $statusBaru = 'disetujui';
                    break;
            
                case 'tolak':
                    $statusBaru = 'ditolak';
                    break;
            }

            $plafondDisetujui = null;
            if($request->filled('plafond_disetujui')){
                $plafondDisetujui = preg_replace('/[^0-9]/','',$request->plafond_disetujui);
            }

            $dataUpdate = ['status'=>$statusBaru,];

            if($validated['aksi']=='setujui'){
                $dataUpdate['plafond_disetujui']    =   $plafondDisetujui;
                $dataUpdate['tenor_disetujui']      =   $validated['tenor_disetujui'];
            }
            $pengajuan->update($dataUpdate);

            ApprovalPengajuan::create([
                'pengajuan_id'      => $pengajuan->id,
                'user_id'           => auth()->id(),
                'role_name'         => auth()->user()->getRoleNames()->first(),
                'aksi'              => $validated['aksi'],
                'status_sebelumnya' => $statusLama,
                'status_sesudahnya' => $statusBaru,
                'catatan'           => $validated['catatan'],
                'plafond_disetujui' => $plafondDisetujui,
                'tenor_disetujui'   => $validated['tenor_disetujui'],
            ]);

        });

        return redirect()->route('pimpinan.dashboard')->with('success', 'Keputusan berhasil disimpan.');
    }
 */
    /**
     * Riwayat approval
     */
    public function history(Pengajuan $pengajuan)
    {
    }
}
