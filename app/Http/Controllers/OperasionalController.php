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
use Illuminate\Support\Facades\Auth;

class OperasionalController extends Controller
{
    
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. IDENTIFIKASI USER DAN PEMBATASAN CABANG
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        $cabangId = null;

        $rolesCabang = [
            'marketing',
            'spvmarketing',
            'admincabang',
        ];

        if ($user->hasAnyRole($rolesCabang)) {
            $karyawan = $user->karyawan;

            if (!$karyawan || !$karyawan->cabang_id) {
                abort(403, 'Akun Anda belum terhubung dengan cabang.');
            }

            $cabangId = (int) $karyawan->cabang_id;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. DAFTAR PEMBIAYAAN
        |--------------------------------------------------------------------------
        */

        $query = Pembiayaan::with([
            'pengajuan.nasabah',
            'pengajuan.marketing',
            'pengajuan.cabang',
            'angsurans',
            'akad.pencairan',
            'pelunasan',
        ])
            ->whereIn('status', ['dicairkan', 'lunas']);

        // Pembatasan cabang berdasarkan akun login.
        if ($cabangId !== null) {
            $query->whereHas('pengajuan', function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        }

        // Pencarian nomor pembiayaan, nama nasabah, dan NIK.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembiayaan', 'like', "%{$search}%")
                    ->orWhereHas('pengajuan.nasabah', function ($nasabah) use ($search) {
                        $nasabah->where('nama', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        // Filter cabang hanya dapat dipilih oleh role yang tidak dibatasi cabang.
        if ($cabangId !== null) {
            $query->whereHas('pengajuan', function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        } elseif ($request->filled('cabang')) {
            $query->whereHas('pengajuan', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang);
            });
        }

        // Filter marketing.
        if ($request->filled('marketing')) {
            $query->whereHas('pengajuan', function ($q) use ($request) {
                $q->where('marketing_id', $request->marketing);
            });
        }

        // Filter status.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembiayaans = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | 3. TOTAL PEMBIAYAAN AKTIF
        |--------------------------------------------------------------------------
        */

        $totalAktif = Pembiayaan::where('status', 'dicairkan')
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('pengajuan', function ($p) use ($cabangId) {
                    $p->where('cabang_id', $cabangId);
                });
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 4. TOTAL PEMBIAYAAN LUNAS
        |--------------------------------------------------------------------------
        */

        $totalLunas = Pembiayaan::where('status', 'lunas')
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('pengajuan', function ($p) use ($cabangId) {
                    $p->where('cabang_id', $cabangId);
                });
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 5. TOTAL OUTSTANDING ANGSURAN
        |--------------------------------------------------------------------------
        */

        $totalOutstanding = Angsuran::whereIn('status', [
            'belum_jatuh_tempo',
            'jatuh_tempo',
        ])
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('pembiayaan.pengajuan', function ($p) use ($cabangId) {
                    $p->where('cabang_id', $cabangId);
                });
            })
            ->sum('sisa_tagihan');

        /*
        |--------------------------------------------------------------------------
        | 6. JATUH TEMPO HARI INI
        |--------------------------------------------------------------------------
        */

        $jatuhTempoHariIni = Angsuran::whereDate(
            'tanggal_jatuh_tempo',
            today()
        )
            ->whereIn('status', [
                'belum_jatuh_tempo',
                'jatuh_tempo',
            ])
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('pembiayaan.pengajuan', function ($p) use ($cabangId) {
                    $p->where('cabang_id', $cabangId);
                });
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 7. JATUH TEMPO BESOK
        |--------------------------------------------------------------------------
        */

        $jatuhTempoBesok = Angsuran::whereDate(
            'tanggal_jatuh_tempo',
            today()->addDay()
        )
            ->whereIn('status', [
                'belum_jatuh_tempo',
                'jatuh_tempo',
            ])
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('pembiayaan.pengajuan', function ($p) use ($cabangId) {
                    $p->where('cabang_id', $cabangId);
                });
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 8. ANGSURAN MENUNGGAK
        |--------------------------------------------------------------------------
        */

        $menunggak = Angsuran::where('status', 'jatuh_tempo')
            ->whereDate('tanggal_jatuh_tempo', '<', today())
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('pembiayaan.pengajuan', function ($p) use ($cabangId) {
                    $p->where('cabang_id', $cabangId);
                });
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 9. PEMBAYARAN ANGSURAN BULAN INI
        |--------------------------------------------------------------------------
        */

        $pembayaranBulanIni = PembayaranAngsuran::whereMonth(
            'tanggal_bayar',
            now()->month
        )
            ->whereYear('tanggal_bayar', now()->year)
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('angsuran.pembiayaan.pengajuan', function ($p) use ($cabangId) {
                    $p->where('cabang_id', $cabangId);
                });
            })
            ->sum('jumlah_bayar');

        /*
        |--------------------------------------------------------------------------
        | 10. PELUNASAN BULAN INI
        |--------------------------------------------------------------------------
        */

        $pelunasanBulanIni = Pelunasan::whereMonth(
            'tanggal_pelunasan',
            now()->month
        )
            ->whereYear('tanggal_pelunasan', now()->year)
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('pembiayaan.pengajuan', function ($p) use ($cabangId) {
                    $p->where('cabang_id', $cabangId);
                });
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 11. OUTSTANDING PER CABANG
        |--------------------------------------------------------------------------
        */

        $outstandingCabangQuery = DB::table('cabangs')
            ->join('pengajuans', 'cabangs.id', '=', 'pengajuans.cabang_id')
            ->join('pembiayaans', 'pengajuans.id', '=', 'pembiayaans.pengajuan_id')
            ->join('angsurans', 'pembiayaans.id', '=', 'angsurans.pembiayaan_id')
            ->whereIn('angsurans.status', [
                'belum_jatuh_tempo',
                'jatuh_tempo',
            ]);

        if ($cabangId !== null) {
            $outstandingCabangQuery->where('cabangs.id', $cabangId);
        }

        $outstandingCabang = $outstandingCabangQuery
            ->groupBy('cabangs.id', 'cabangs.nama_cabang')
            ->selectRaw('
                cabangs.id,
                cabangs.nama_cabang,
                SUM(angsurans.sisa_tagihan) as outstanding
            ')
            ->orderByDesc('outstanding')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 12. OUTSTANDING PER MARKETING
        |--------------------------------------------------------------------------
        */

        $outstandingMarketingQuery = DB::table('users')
            ->join('pengajuans', 'users.id', '=', 'pengajuans.marketing_id')
            ->join('pembiayaans', 'pengajuans.id', '=', 'pembiayaans.pengajuan_id')
            ->join('angsurans', 'pembiayaans.id', '=', 'angsurans.pembiayaan_id')
            ->whereIn('angsurans.status', [
                'belum_jatuh_tempo',
                'jatuh_tempo',
            ]);

        if ($cabangId !== null) {
            $outstandingMarketingQuery->where(
                'pengajuans.cabang_id',
                $cabangId
            );
        }

        $outstandingMarketing = $outstandingMarketingQuery
            ->groupBy('users.id', 'users.name')
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(angsurans.sisa_tagihan) as outstanding')
            )
            ->orderByDesc('outstanding')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 13. DROPDOWN CABANG
        |--------------------------------------------------------------------------
        */

        $cabangs = Cabang::when(
            $cabangId !== null,
            function ($q) use ($cabangId) {
                $q->where('id', $cabangId);
            }
        )
            ->orderBy('nama_cabang')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 14. DROPDOWN MARKETING DAN SPV MARKETING
        |--------------------------------------------------------------------------
        */

        $marketings = User::role([
            'marketing',
            'spvmarketing',
        ])
            ->when($cabangId !== null, function ($q) use ($cabangId) {
                $q->whereHas('karyawan', function ($k) use ($cabangId) {
                    $k->where('cabang_id', $cabangId);
                });
            })
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 15. TAMPILKAN HALAMAN
        |--------------------------------------------------------------------------
        */

        return view('operasional.index', compact(
            'pembiayaans',
            'totalAktif',
            'totalLunas',
            'totalOutstanding',
            'jatuhTempoHariIni',
            'jatuhTempoBesok',
            'menunggak',
            'pembayaranBulanIni',
            'pelunasanBulanIni',
            'outstandingCabang',
            'outstandingMarketing',
            'cabangs',
            'marketings'
        ));
    }


    public function show(Pembiayaan $pembiayaan)
    {
        $pembiayaan->load([

            /*
            |--------------------------------------------------------------------------
            | PENGAJUAN
            |--------------------------------------------------------------------------
            */

            'pengajuan.nasabah',
            'pengajuan.nasabah.pekerjaanNasabah',

            'pengajuan.referensis',
            'pengajuan.referensis.pekerjaan',

            /*
            | Dokumen pengajuan
            */

            'pengajuan.dokumenPengajuans',

            /*
            | Dokumen payroll
            */

            'pengajuan.dokumenPayrolls',

            /*
            | Marketing & Cabang
            */

            'pengajuan.marketing.user',
            'pengajuan.cabang',

            /*
            | Analisa
            */

            'pengajuan.analisa',

            /*
            | Jaminan + Dokumen Jaminan
            */

            'pengajuan.jaminanPengajuans',
            'pengajuan.jaminanPengajuans.dokumenJaminans',

            /*
            | Kapital
            */

            'pengajuan.kapital',

            /*
            | Approval
            */

            'pengajuan.approvals.user',


            /*
            |--------------------------------------------------------------------------
            | SURVEY
            |--------------------------------------------------------------------------
            */

            'pengajuan.survey',

            /*
            | Berkas Survey
            */

            'pengajuan.survey.berkas',

            /*
            | Dokumentasi Survey
            */

            'pengajuan.survey.dokumentasis',

            /*
            |--------------------------------------------------------------------------
            | TOP UP / REPEAT ORDER
            |--------------------------------------------------------------------------
            */

            'pembiayaanLama',
            'pembiayaanLama.pengajuan.nasabah',


            /*
            |--------------------------------------------------------------------------
            | AKAD & PENCAIRAN
            |--------------------------------------------------------------------------
            */

            'akad',
            'akad.pencairan',


            /*
            |--------------------------------------------------------------------------
            | ANGSURAN & PELUNASAN
            |--------------------------------------------------------------------------
            */

            'angsurans',
            'pelunasan',


            /*
            |--------------------------------------------------------------------------
            | AUDIT TRAIL
            |--------------------------------------------------------------------------
            */

            'auditTrails.user',

        ]);


        /*
        |--------------------------------------------------------------------------
        | REFERENSI
        |--------------------------------------------------------------------------
        */

        $referensis =
            $pembiayaan->pengajuan->referensis;


        $pasangan =
            $referensis->firstWhere(
                'jenis',
                'pasangan'
            );


        $penjamin =
            $referensis->firstWhere(
                'jenis',
                'penjamin'
            );


        $saudaras =
            $referensis->where(
                'jenis',
                'saudara'
            );


        /*
        |--------------------------------------------------------------------------
        | DOKUMENTASI SURVEY
        |--------------------------------------------------------------------------
        */

        $survey =
            $pembiayaan->pengajuan->survey;


        $rumah = collect();

        $usaha = collect();

        $jaminan = collect();

        $videos = collect();


        if ($survey) {

            $rumah =
                $survey->dokumentasis
                    ->where('kategori', 'rumah');

            $usaha =
                $survey->dokumentasis
                    ->where('kategori', 'usaha');

            $jaminan =
                $survey->dokumentasis
                    ->where('kategori', 'jaminan');

            $videos =
                $survey->dokumentasis
                    ->where('kategori', 'video');
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('operasional.show', [

            'pembiayaan' => $pembiayaan,

            'pasangan' => $pasangan,

            'penjamin' => $penjamin,

            'saudaras' => $saudaras,

            /*
            | Survey
            */

            'survey' => $survey,

            'rumah' => $rumah,

            'usaha' => $usaha,

            'jaminan' => $jaminan,

            'videos' => $videos,

        ]);
    }
}
