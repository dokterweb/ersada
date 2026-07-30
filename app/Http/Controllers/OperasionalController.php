<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\Cabang;
use App\Models\Pelunasan;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperasionalController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembiayaan::with(['pengajuan.nasabah','pengajuan.marketing','pengajuan.cabang','angsurans','akad.pencairan','pelunasan'])
        ->whereIn('status', ['dicairkan','lunas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembiayaan', 'like', "%{$search}%")
                    ->orWhereHas('pengajuan.nasabah', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('cabang')) {
            $query->whereHas('pengajuan', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang);
            });
        }

        if ($request->filled('marketing')) {
            $query->whereHas('pengajuan', function ($q) use ($request) {
                $q->where('marketing_id', $request->marketing);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembiayaans = $query->latest()->paginate(20)->withQueryString();

        $totalAktif = Pembiayaan::where('status', 'dicairkan')->count();

        $totalLunas = Pembiayaan::where('status', 'lunas')->count();

        $totalOutstanding = Angsuran::whereIn('status', ['belum_jatuh_tempo','jatuh_tempo'])->sum('sisa_tagihan');

        $jatuhTempoHariIni = Angsuran::whereDate('tanggal_jatuh_tempo',today())
        ->whereIn('status', ['belum_jatuh_tempo','jatuh_tempo'])->count();

        $jatuhTempoBesok = Angsuran::whereDate('tanggal_jatuh_tempo',today()->addDay())
        ->whereIn('status',['belum_jatuh_tempo','jatuh_tempo'])->count();

        $menunggak = Angsuran::where('status','jatuh_tempo')
        ->whereDate('tanggal_jatuh_tempo','<',today())->count();

        $pembayaranBulanIni = PembayaranAngsuran::whereMonth('tanggal_bayar',now()->month)->sum('jumlah_bayar');
        
        $pelunasanBulanIni = Pelunasan::whereMonth('tanggal_pelunasan',now()->month)->count();

        $outstandingCabang = DB::table('cabangs')
            ->join('pengajuans','cabangs.id','=','pengajuans.cabang_id')
            ->join('pembiayaans','pengajuans.id','=','pembiayaans.pengajuan_id')
            ->join('angsurans','pembiayaans.id','=','angsurans.pembiayaan_id')
            ->whereIn('angsurans.status',[
                'belum_jatuh_tempo',
                'jatuh_tempo'
            ])
            ->groupBy('cabangs.id','cabangs.nama_cabang')
            ->selectRaw('
                cabangs.nama_cabang,
                SUM(angsurans.sisa_tagihan) as outstanding
            ')
            ->orderByDesc('outstanding')->get();

        $outstandingMarketing = DB::table('users')
            ->join('pengajuans', 'users.id', '=', 'pengajuans.marketing_id')
            ->join('pembiayaans', 'pengajuans.id', '=', 'pembiayaans.pengajuan_id')
            ->join('angsurans', 'pembiayaans.id', '=', 'angsurans.pembiayaan_id')
            ->whereIn('angsurans.status', [
                'belum_jatuh_tempo',
                'jatuh_tempo'
            ])
            ->groupBy('users.id','users.name')
            ->select('users.id','users.name',
                DB::raw('SUM(angsurans.sisa_tagihan) as outstanding')
            )
            ->orderByDesc('outstanding')->get();

        $cabangs = Cabang::orderBy('nama_cabang')->get();

        $marketings = User::role('marketing')->orderBy('name')->get();

        return view('operasional.index', compact('pembiayaans','totalAktif','totalLunas','totalOutstanding','jatuhTempoHariIni','jatuhTempoBesok','menunggak',
            'pembayaranBulanIni','pelunasanBulanIni','outstandingCabang','outstandingMarketing','cabangs','marketings'));
    }

    public function show(Pembiayaan $pembiayaan)
    {
        $pembiayaan->load(['pengajuan.nasabah','pengajuan.marketing','pengajuan.cabang','akad','akad.pencairan','angsurans','pelunasan','auditTrails.user',]);
        return view('operasional.show', compact('pembiayaan'));
    }
}
