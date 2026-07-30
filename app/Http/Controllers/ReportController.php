<?php

namespace App\Http\Controllers;

use App\Exports\AngsuranExport;
use App\Exports\JatuhTempoExport;
use App\Exports\NplExport;
use App\Exports\OutstandingExport;
use App\Exports\PelunasanExport;
use App\Exports\PembiayaanExport;
use App\Exports\PencairanExport;
use App\Models\Angsuran;
use App\Models\Pelunasan;
use App\Models\Pembiayaan;
use App\Models\Pencairan;
use App\Services\PdfService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $service,
        protected PdfService $pdfService
        ){}

    public function pembiayaan(Request $request)
    {
        $pembiayaans = $this->service
            ->pembiayaanQuery($request)
            ->latest()
            ->paginate(20)
            ->withQueryString();
    
        $filters = $this->service->getFilterData();
    
        return view(
            'reports.pembiayaan',
            array_merge([
                'pembiayaans'      => $pembiayaans,
                'exportExcelRoute' => 'reports.pembiayaan.excel',
                'exportPdfRoute'   => 'reports.pembiayaan.pdf',
            ], $filters)
        );
    }

    public function exportPembiayaanExcel(Request $request)
    {
        $data = $this->service->pembiayaanQuery($request)->latest()->get();
    
        return Excel::download(new PembiayaanExport($data),'Laporan-Pembiayaan.xlsx');
    }

    public function exportPembiayaanPdf(Request $request)
    {
        $data = $this->service->pembiayaanQuery($request)->latest()->get();
    
        return $this->pdfService->laporanPembiayaan($data, $request);
    }


    public function pencairan(Request $request)
    {
        $filters = $this->service->getFilterData();
    
        $pencairans = $this->service->pencairanQuery($request)->latest()->paginate(20)->withQueryString();
    
        return view(
            'reports.pencairan',
            array_merge([
                'pencairans' => $pencairans,
                'exportExcelRoute' => 'reports.pencairan.excel',
                'exportPdfRoute' => 'reports.pencairan.pdf',
            ], $filters)
        );
    }
    

    public function exportPencairanPdf(Request $request)
    {
        $data = $this->service->pencairanQuery($request)->latest()->get();
    
        return $this->pdfService->laporanPencairan($data, $request);
    }
    
    
    public function exportPencairanExcel(Request $request)
    {
        $data = $this->service->pencairanQuery($request)->latest()->get();

        return Excel::download(new PencairanExport($data),'Laporan-Pencairan.xlsx');
    }
    
    public function angsuran(Request $request)
    {
        $filters = $this->service->getFilterData();

        $angsurans = $this->service
            ->angsuranQuery($request)
            ->latest()->paginate(20)->withQueryString();

        return view('reports.angsuran',
            array_merge([
                'angsurans' => $angsurans,
                'exportExcelRoute' => 'reports.angsuran.excel',
                'exportPdfRoute' => 'reports.angsuran.pdf',
            ], $filters)
        );
    }

    public function exportAngsuranExcel(Request $request)
    {
        $data = $this->service->angsuranQuery($request)->latest()->get();

        return Excel::download(new AngsuranExport($data),'Laporan-Angsuran.xlsx');
    }

    public function exportAngsuranPdf(Request $request)
    {
        $data = $this->service->angsuranQuery($request)->latest()->get();

        return $this->pdfService->laporanAngsuran($data, $request);
    }

    public function pelunasan(Request $request)
    {
        $filters = $this->service->getFilterData();

        $pelunasans = $this->service->pelunasanQuery($request)
            ->latest()->paginate(20)->withQueryString();

        return view('reports.pelunasan',
            array_merge([
                'pelunasans' => $pelunasans,
                'exportExcelRoute' => 'reports.pelunasan.excel',
                'exportPdfRoute' => 'reports.pelunasan.pdf',
            ], $filters)
        );
    }

    public function exportPelunasanExcel(Request $request)
    {
        $data = $this->service->pelunasanQuery($request)->latest()->get();
        return Excel::download(new PelunasanExport($data),'Laporan-Pelunasan.xlsx');
    }

    public function exportPelunasanPdf(Request $request)
    {
        $data = $this->service->pelunasanQuery($request)->latest()->get();
        return $this->pdfService->laporanPelunasan($data, $request);
    }

    public function outstanding(Request $request)
    {
        $filters = $this->service->getFilterData();
    
        $pembiayaans = $this->service
            ->outstandingQuery($request)
            ->latest()
            ->paginate(20)
            ->withQueryString();
    
        return view(
            'reports.outstanding',
            array_merge([
                'pembiayaans' => $pembiayaans,
                'exportExcelRoute' => 'reports.outstanding.excel',
                'exportPdfRoute' => 'reports.outstanding.pdf',
            ], $filters)
        );
    }

    public function exportOutstandingExcel(Request $request)
    {
        $data = $this->service->outstandingQuery($request)->latest()->get();
        return Excel::download(new OutstandingExport($data),'Laporan-Outstanding.xlsx');
    }

    public function exportOutstandingPdf(Request $request)
    {
        $data = $this->service->outstandingQuery($request)->latest()->get();

        return $this->pdfService
            ->laporanOutstanding($data, $request);
    }

  public function jatuhTempo(Request $request)
    {
        $filters = $this->service->getFilterData();

        $angsurans = $this->service->jatuhTempoQuery($request)
            ->latest('tanggal_jatuh_tempo')->paginate(20)->withQueryString();

        return view('reports.jatuh-tempo',
            array_merge([
                'angsurans' => $angsurans,
                'exportExcelRoute' => 'reports.jatuhtempo.excel',
                'exportPdfRoute' => 'reports.jatuhtempo.pdf',
            ], $filters)
        );
    }

    public function exportJatuhTempoExcel(Request $request)
    {
        $data = $this->service->jatuhTempoQuery($request)
            ->latest('tanggal_jatuh_tempo')->get();
    
        return Excel::download(new JatuhTempoExport($data),'Laporan-Jatuh-Tempo.xlsx');
    }

    public function exportJatuhTempoPdf(Request $request)
    {
        $data = $this->service->jatuhTempoQuery($request)
            ->latest('tanggal_jatuh_tempo')->get();
    
        return $this->pdfService->laporanJatuhTempo($data, $request);
    }

    public function npl(Request $request)
    {
        $filters = $this->service->getFilterData();
    
        $angsurans = $this->service->nplQuery($request)->orderBy('tanggal_jatuh_tempo')->paginate(20)->withQueryString();
    
        return view('reports.npl',
            array_merge([
                'angsurans' => $angsurans,
                'exportExcelRoute' => 'reports.npl.excel',
                'exportPdfRoute' => 'reports.npl.pdf',
            ], $filters)
        );
    }

    public function exportNplExcel(Request $request)
    {
        $data = $this->service->nplQuery($request)->orderBy('tanggal_jatuh_tempo')->get();
        return Excel::download(new NplExport($data),'Laporan-NPL.xlsx');
    }

    public function exportNplPdf(Request $request)
    {
        $data = $this->service->nplQuery($request)->orderBy('tanggal_jatuh_tempo')->get();
    
        return $this->pdfService->laporanNpl($data, $request);
    }    
}
