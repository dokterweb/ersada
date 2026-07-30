<?php

namespace App\Http\Controllers;
use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
 
    public function indexsuperadmin()
    {
        // KPI

        $totalAktif = Pembiayaan::where('status', 'dicairkan')->count();
        $totalLunas = Pembiayaan::where('status', 'lunas')->count();
        $pembayaranBulanIni = PembayaranAngsuran::whereMonth('tanggal_bayar',now()->month)->sum('jumlah_bayar');
        $menunggak = Angsuran::where('status','jatuh_tempo')->whereDate('tanggal_jatuh_tempo','<',today())->count();
        // Grafik
        $pembayaranBulanan = DB::table('pembayaran_angsurans')
        ->selectRaw('
            MONTH(tanggal_bayar) as bulan,
            SUM(jumlah_bayar) as total
        ')
        ->whereYear('tanggal_bayar', now()->year)
        ->groupByRaw('MONTH(tanggal_bayar)')
        ->orderByRaw('MONTH(tanggal_bayar)')->get();

        $pencairanBulanan = DB::table('pencairans')
        ->selectRaw('
            MONTH(tanggal_pencairan) as bulan,
            SUM(jumlah_dicairkan) as total
        ')
        ->whereYear('tanggal_pencairan', now()->year)
        ->groupByRaw('MONTH(tanggal_pencairan)')
        ->orderByRaw('MONTH(tanggal_pencairan)')->get();

        $pelunasanBulanan = DB::table('pelunasans')
        ->selectRaw('
            MONTH(tanggal_pelunasan) as bulan,
            SUM(total_pelunasan) as total
        ')
        ->whereYear('tanggal_pelunasan', now()->year)
        ->groupByRaw('MONTH(tanggal_pelunasan)')
        ->orderByRaw('MONTH(tanggal_pelunasan)')->get();

        // Outstanding


        $outstandingCabang = DB::table('cabangs')

        ->join('pengajuans', 'cabangs.id', '=', 'pengajuans.cabang_id')

        ->join('pembiayaans', 'pengajuans.id', '=', 'pembiayaans.pengajuan_id')

        ->join('angsurans', 'pembiayaans.id', '=', 'angsurans.pembiayaan_id')

        ->whereIn('angsurans.status', [
            'belum_jatuh_tempo',
            'jatuh_tempo'
        ])

        ->groupBy(
            'cabangs.id',
            'cabangs.nama_cabang'
        )

        ->select(
            'cabangs.id',
            'cabangs.nama_cabang',
            DB::raw('SUM(angsurans.sisa_tagihan) as outstanding')
        )

        ->orderByDesc('outstanding')

        ->limit(10)

        ->get();

        $outstandingMarketing = DB::table('users')

        ->join('pengajuans', 'users.id', '=', 'pengajuans.marketing_id')

        ->join('pembiayaans', 'pengajuans.id', '=', 'pembiayaans.pengajuan_id')

        ->join('angsurans', 'pembiayaans.id', '=', 'angsurans.pembiayaan_id')

        ->whereIn('angsurans.status', [
            'belum_jatuh_tempo',
            'jatuh_tempo'
        ])

        ->groupBy(
            'users.id',
            'users.name'
        )

        ->select(
            'users.id',
            'users.name',
            DB::raw('SUM(angsurans.sisa_tagihan) as outstanding')
        )

        ->orderByDesc('outstanding')

        ->limit(10)

        ->get();

        $totalOutstanding = $outstandingCabang->sum('outstanding');
        $outstandingCabang->transform(function ($item) use ($totalOutstanding) {
            $item->persentase = $totalOutstanding > 0
                ? round(($item->outstanding / $totalOutstanding) * 100, 1): 0;
            return $item;
        });

        return view('dashboard.superadmin',compact('totalAktif','totalLunas','pembayaranBulanIni','menunggak',
        'pembayaranBulanan','pencairanBulanan','pelunasanBulanan','outstandingCabang','outstandingMarketing'));
    }

    public function indexmarketing()
    {
        $user = Auth::user();
    
        $karyawan = $user->karyawan;
    
        if (!$karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }
    
        $query = Pengajuan::where('marketing_id', auth()->user()->getMarketingId());
    
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
