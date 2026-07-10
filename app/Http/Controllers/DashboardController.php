<?php

namespace App\Http\Controllers;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
 
    public function indexsuperadmin()
    {
        return view('dashboard.superadmin');  // Halaman dashboard admin
    }

    public function indexmarketing()
    {
        $user = Auth::user();
    
        $karyawan = $user->karyawan;
    
        if (!$karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }
    
        $query = Pengajuan::where('marketing_id', $karyawan->cabang_id);
    
        $draft = (clone $query)->where('status', 'draft')->count();
    
        $menungguPimpinan = (clone $query)->where('status','menunggu_pimpinan')->count();
    
        $revisi = (clone $query)->whereIn('status', ['revisi_marketing','revisi_spv_marketing',])->count();
    
        $disetujui = (clone $query)->where('status', 'disetujui_direktur')->count();
    
        $ditolak = (clone $query)
            ->where(function ($q) {
                $q->where('status', 'ditolak_spv_marketing')
                  ->orWhere('status', 'ditolak_spv_survey')
                  ->orWhere('status', 'ditolak_kacab')
                  ->orWhere('status', 'ditolak_direktur');
            })->count();
    
        $pengajuanTerbaru = (clone $query)
            ->with(['nasabah','marketing.user',])->latest()->take(10)->get();
    
        return view('dashboard.marketing', compact('draft','menungguPimpinan','revisi','disetujui','ditolak','pengajuanTerbaru'));
    }
}
