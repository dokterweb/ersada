<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\Cabang;
use App\Models\Pelunasan;
use App\Models\Pembiayaan;
use App\Models\Pencairan;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;
use App\Exports\PembiayaanExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(protected ReportService $service){}

    public function pembiayaan(Request $request)
    {
        $query = Pembiayaan::with(['pengajuan.nasabah','pengajuan.cabang','pengajuan.marketing.user','angsurans']);
        
        $query = $this->service->applyScope($query);
        
        //Filter
        $this->service->filterTanggal($query,$request,'created_at');
        if ($request->filled('cabang')) {
            $query->whereHas('pengajuan', function ($q) use ($request) {
                $q->where('cabang_id',$request->cabang);
            });
        }
        
        if ($request->filled('marketing')) {
            $query->whereHas('pengajuan', function ($q) use ($request) {
                $q->where('marketing_id',$request->marketing);
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status',$request->status);
        }
        
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword){
                $q->where('nomor_pembiayaan','like',"%{$keyword}%")
                ->orWhereHas('pengajuan.nasabah',function($qq) use($keyword){
                    $qq->where('nama','like',"%{$keyword}%")
                       ->orWhere('nik','like',"%{$keyword}%");
                });
            });
        }
        
        $pembiayaans = $query->latest()->paginate(20)->withQueryString();
        
        // Filter UI
        
        $filters = $this->service->getFilterData();
        
        return view('reports.pembiayaan',
            array_merge([
                'pembiayaans' => $pembiayaans,
                'exportExcelRoute' => 'reports.pembiayaan.excel',
                'exportPdfRoute' => 'reports.pembiayaan.pdf',
            ], $filters)
        );
    }

    public function exportPembiayaanExcel(Request $request)
    {
        $query = Pembiayaan::with([
            'pengajuan.nasabah',
            'pengajuan.cabang',
            'pengajuan.marketing.user',
            'angsurans'
        ]);

        $query = $this->service->applyScope($query);

        $this->service->filterTanggal(
            $query,
            $request,
            'created_at'
        );

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

        if ($request->filled('keyword')) {

            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {

                $q->where('nomor_pembiayaan', 'like', "%{$keyword}%")
                    ->orWhereHas('pengajuan.nasabah', function ($qq) use ($keyword) {

                        $qq->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('nik', 'like', "%{$keyword}%");

                    });

            });

        }

        return Excel::download(

            new PembiayaanExport($query->get()),

            'Laporan-Pembiayaan.xlsx'

        );
    }

    public function pencairan(Request $request)
    {
        $query = Pencairan::with([
            'akad.pembiayaan.pengajuan.nasabah',
            'akad.pembiayaan.pengajuan.cabang',
            'akad.pembiayaan.pengajuan.marketing.user',
            'creator'
        ]);

        // Hak Akses
        $query = $this->service->applyScopePencairan($query);

        // Filter Tanggal
        $this->service->filterTanggal($query,$request,'tanggal_pencairan');

        // Cabang
        if ($request->filled('cabang')) {
            $query->whereHas('akad.pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang);
            });
        }

        //Marketing
        if ($request->filled('marketing')) {
            $query->whereHas('akad.pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('marketing_id', $request->marketing);
            });
        }

        // Keyword
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_pencairan', 'like', "%{$keyword}%")
                ->orWhereHas('akad.pembiayaan', function ($qq) use ($keyword) {
                        $qq->where('nomor_pembiayaan', 'like', "%{$keyword}%");
                })
                ->orWhereHas('akad.pembiayaan.pengajuan.nasabah', function ($qq) use ($keyword) {
                        $qq->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('nik', 'like', "%{$keyword}%");
                });
            });
        }

        $pencairans = $query->latest('tanggal_pencairan')->paginate(20)->withQueryString();

        $filters = $this->service->getFilterData();

        // return view('reports.pencairan',array_merge(['pencairans' => $pencairans],$filters));
        return view('reports.pencairan',
            array_merge([
                'pencairans' => $pencairans,
                'exportExcelRoute' => 'reports.pencairan.excel',
                'exportPdfRoute' => 'reports.pencairan.pdf',
            ], $filters)
        );
    }

    public function angsuran(Request $request)
    {
        $query = Angsuran::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.marketing.user'
        ]);
        $query = $this->service->applyScopeAngsuran($query);

        $this->service->filterTanggal($query,$request,'tanggal_jatuh_tempo');

        if ($request->filled('cabang')) {
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang);
            });
        }

        if ($request->filled('marketing')) {
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('marketing_id', $request->marketing);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('pembiayaan', function ($q) use ($keyword){
                $q->where('nomor_pembiayaan','like',"%{$keyword}%");
            })
            ->orWhereHas('pembiayaan.pengajuan.nasabah', function ($q) use ($keyword){
                $q->where('nama','like',"%{$keyword}%")
                ->orWhere('nik','like',"%{$keyword}%");
            });
        }

        $angsurans = $query->orderBy('tanggal_jatuh_tempo')->paginate(20)->withQueryString();

        $filters = $this->service->getFilterData();

        return view('reports.angsuran',array_merge(['angsurans' => $angsurans],$filters));
    }

    public function pelunasan(Request $request)
    {
        $query = Pelunasan::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.marketing.user',
            'creator'
        ]);
    
        $query = $this->service->applyScopePelunasan($query);
    
        if (!$request->filled('tanggal_awal')) {
            $request->merge([
                'tanggal_awal' => now()->startOfMonth()->toDateString(),
            ]);
        }
    
        if (!$request->filled('tanggal_akhir')) {
            $request->merge([
                'tanggal_akhir' => now()->endOfMonth()->toDateString(),
            ]);
        }
    
        $query->whereBetween('tanggal_pelunasan', [
            $request->tanggal_awal,
            $request->tanggal_akhir,
        ]);
    
        if ($request->filled('cabang')) {
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang);
            });
        }
    
        if ($request->filled('marketing')) {
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('marketing_id', $request->marketing);
            });
        }
    
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_pelunasan', 'like', "%{$keyword}%")
                ->orWhereHas('pembiayaan', function ($qq) use ($keyword) {
                    $qq->where('nomor_pembiayaan', 'like', "%{$keyword}%");
                })
                ->orWhereHas('pembiayaan.pengajuan.nasabah', function ($qq) use ($keyword) {
                    $qq->where('nama', 'like', "%{$keyword}%")
                       ->orWhere('nik', 'like', "%{$keyword}%");
                });
            });
        }
    
        $pelunasans = $query->latest('tanggal_pelunasan')->paginate(20)->withQueryString();
    
        $filters = $this->service->getFilterData();
    
        return view('reports.pelunasan',array_merge(['pelunasans' => $pelunasans,], $filters));
    }

    public function outstanding(Request $request)
    {
        $query = Pembiayaan::with(['pengajuan.nasabah','pengajuan.cabang','pengajuan.marketing.user','angsurans']);
    
        $query = $this->service->applyScope($query);
        $query->where('status','dicairkan');

        if($request->filled('cabang')){
            $query->whereHas('pengajuan',function($q) use($request){
                $q->where('cabang_id',$request->cabang);
            });
        }
    
        if($request->filled('marketing')){
            $query->whereHas('pengajuan',function($q) use($request){
                $q->where('marketing_id',$request->marketing);
            });
        }
    
        if($request->filled('keyword')){
            $keyword=$request->keyword;
            $query->where('nomor_pembiayaan','like',"%$keyword%")
            ->orWhereHas('pengajuan.nasabah',function($q) use($keyword){
                $q->where('nama','like',"%$keyword%")
                  ->orWhere('nik','like',"%$keyword%");
            });
        }
    
        $pembiayaans=$query->latest()->paginate(20)->withQueryString();
    
        $filters = $this->service->getFilterData();
    
        return view('reports.outstanding',array_merge(['pembiayaans'=>$pembiayaans],$filters));
    
    }

    public function jatuhTempo(Request $request)
    {
        $query = Angsuran::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.marketing.user'
        ]);
    
        $query = $this->service->applyScopeAngsuran($query);
    
        if (!$request->filled('tanggal_awal')) {
            $request->merge([
                'tanggal_awal' => now()->startOfMonth()->toDateString(),
            ]);
        }
    
        if (!$request->filled('tanggal_akhir')) {
            $request->merge([
                'tanggal_akhir' => now()->endOfMonth()->toDateString(),
            ]);
        }
    
        $query->whereBetween('tanggal_jatuh_tempo', [
            $request->tanggal_awal,
            $request->tanggal_akhir,
        ]);
    
        $query->whereIn('status', ['belum_jatuh_tempo','jatuh_tempo']);
    
        if ($request->filled('cabang')) {
    
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang);
            });
    
        }

        if ($request->filled('marketing')) {
    
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('marketing_id', $request->marketing);
            });
    
        }
    
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('pembiayaan', function ($qq) use ($keyword) {
                    $qq->where('nomor_pembiayaan', 'like', "%{$keyword}%");
                });
                $q->orWhereHas('pembiayaan.pengajuan.nasabah', function ($qq) use ($keyword) {
                    $qq->where('nama', 'like', "%{$keyword}%")
                       ->orWhere('nik', 'like', "%{$keyword}%");
                });
            });
        }
    
        $angsurans = $query->orderBy('tanggal_jatuh_tempo')->paginate(20)->withQueryString();
    
        $filters = $this->service->getFilterData();
    
        return view('reports.jatuh-tempo',array_merge(['angsurans' => $angsurans], $filters));
    }

    public function npl(Request $request)
    {
        $query = Angsuran::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.marketing.user'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Scope
        |--------------------------------------------------------------------------
        */

        $query = $this->service->applyScopeAngsuran($query);

        /*
        |--------------------------------------------------------------------------
        | Hanya yang belum dibayar
        |--------------------------------------------------------------------------
        */

        $query->whereIn('status',[
            'belum_jatuh_tempo',
            'jatuh_tempo'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sudah lewat jatuh tempo
        |--------------------------------------------------------------------------
        */

        $query->whereDate(
            'tanggal_jatuh_tempo',
            '<',
            now()
        );

        /*
        |--------------------------------------------------------------------------
        | Cabang
        |--------------------------------------------------------------------------
        */

        if($request->filled('cabang')){

            $query->whereHas('pembiayaan.pengajuan',function($q) use($request){

                $q->where('cabang_id',$request->cabang);

            });

        }

        /*
        |--------------------------------------------------------------------------
        | Marketing
        |--------------------------------------------------------------------------
        */

        if($request->filled('marketing')){

            $query->whereHas('pembiayaan.pengajuan',function($q) use($request){

                $q->where('marketing_id',$request->marketing);

            });

        }

        /*
        |--------------------------------------------------------------------------
        | Keyword
        |--------------------------------------------------------------------------
        */

        if($request->filled('keyword')){

            $keyword=$request->keyword;

            $query->where(function($q) use($keyword){

                $q->whereHas('pembiayaan',function($qq) use($keyword){

                    $qq->where('nomor_pembiayaan','like',"%$keyword%");

                });

                $q->orWhereHas('pembiayaan.pengajuan.nasabah',function($qq) use($keyword){

                    $qq->where('nama','like',"%$keyword%")
                    ->orWhere('nik','like',"%$keyword%");

                });

            });

        }

        $angsurans = $query
            ->orderBy('tanggal_jatuh_tempo')
            ->paginate(20)
            ->withQueryString();

        $filters = $this->service->getFilterData();

        return view(
            'reports.npl',
            array_merge([
                'angsurans'=>$angsurans
            ],$filters)
        );

    }
}
