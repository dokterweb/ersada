<?php

namespace App\Services;

use App\Models\Angsuran;
use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Pelunasan;
use App\Models\Pembiayaan;
use App\Models\Pencairan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportService
{
    public function applyScope($query, string $pengajuanRelation = 'pengajuan')
    {
        $user = auth()->user();

        if ($user->hasRole('marketing')) {

            $marketingId = $user->karyawan?->id;

            $query->whereHas($pengajuanRelation, function ($q) use ($marketingId) {
                $q->where('marketing_id', $marketingId);
            });

        } elseif ($user->hasRole('admin')) {

            $cabangId = $user->karyawan?->cabang_id;

            $query->whereHas($pengajuanRelation, function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });

        }

        return $query;
    }
    /**
     * Filter tanggal.
     */
    public function filterTanggal($query, $request, string $field): void
    {
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
    
        $query->whereBetween($field, [
            $request->tanggal_awal,
            $request->tanggal_akhir,
        ]);
    }
    
    public function getFilterData(): array
    {
        $user = auth()->user();
        //Default
        $showCabang = false;
        $showMarketing = false;
        $cabangs = collect();
        $marketings = collect();

        // Marketing
        if ($user->hasRole('marketing')) {
            return compact('showCabang','showMarketing','cabangs','marketings');
        }
    
        // Admin Cabang
        if ($user->hasRole('admin')) {
            $showMarketing = true;
            $marketings = Karyawan::with('user')->where('cabang_id', $user->karyawan->cabang_id)->get();
    
            return compact('showCabang','showMarketing','cabangs','marketings');
        }
    
        //Pimpinan / Superadmin
        $showCabang = true;
        $showMarketing = true;
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $marketings = Karyawan::marketing()->get();
    
        return compact('showCabang','showMarketing','cabangs','marketings');
    }

    public function applyScopePencairan($query)
    {
        $user = auth()->user();
        if ($user->hasRole('marketing')) {
            $marketingId = $user->getMarketingId();
            $query->whereHas('akad.pembiayaan.pengajuan', function ($q) use ($marketingId) {
                $q->where('marketing_id', $marketingId);
            });
        }
        elseif ($user->hasRole('admin')) {
            $cabangId = $user->getCabangId();
            $query->whereHas('akad.pembiayaan.pengajuan', function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        }
        return $query;
    }

    public function applyScopeAngsuran($query)
    {
        $user = auth()->user();
        if ($user->hasRole('marketing')) {
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($user){
                $q->where(
                    'marketing_id',
                    $user->getMarketingId()
                );
            });

        }
        elseif ($user->hasRole('admin')) {
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($user){
                $q->where(
                    'cabang_id',
                    $user->getCabangId()
                );
            });
        }
        return $query;
    }

    public function applyScopePelunasan($query)
    {
        $user = auth()->user();
        if ($user->hasRole('marketing')) {
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($user) {
                $q->where(
                    'marketing_id',
                    $user->getMarketingId()
                );
            });
        }
        elseif ($user->hasRole('admin')) {
            $query->whereHas('pembiayaan.pengajuan', function ($q) use ($user) {
                $q->where(
                    'cabang_id',
                    $user->getCabangId()
                );
            });
        }
        return $query;
    }

    public function pembiayaanQuery(Request $request)
    {
        $query = Pembiayaan::with([
            'pengajuan.nasabah',
            'pengajuan.cabang',
            'pengajuan.marketing.user',
            'angsurans'
        ]);
    
        $query = $this->applyScope($query);
    
        $this->filterTanggal(
            $query,
            $request,
            'created_at' // atau 'tanggal_akad'
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
        return $query;
    }    

    public function pencairanQuery(Request $request)
    {
        $query = Pencairan::with([
            'akad',
            'akad.pembiayaan',
            'akad.pembiayaan.pengajuan.nasabah',
            'akad.pembiayaan.pengajuan.cabang',
            'akad.pembiayaan.pengajuan.marketing.user',
        ]);

        $this->applyScope($query,'akad.pembiayaan.pengajuan');

        $this->filterTanggal($query, $request, 'tanggal_pencairan');

        if ($request->filled('cabang')) {
            $query->whereHas('akad.pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang);
            });
        }

        if ($request->filled('marketing')) {
            $query->whereHas('akad.pembiayaan.pengajuan', function ($q) use ($request) {
                $q->where('marketing_id', $request->marketing);
            });
        }

        if ($request->filled('keyword')) {

            $keyword = $request->keyword;
        
            $query->where(function ($q) use ($keyword) {
        
                $q->whereHas('akad.pembiayaan', function ($sub) use ($keyword) {
                    $sub->where('nomor_pembiayaan', 'like', "%{$keyword}%");
                });
        
                $q->orWhereHas('akad.pembiayaan.pengajuan.nasabah', function ($sub) use ($keyword) {
                    $sub->where('nama', 'like', "%{$keyword}%");
                });
        
                $q->orWhere('nomor_pencairan', 'like', "%{$keyword}%");
        
            });
        
        }

        return $query;
    }

    public function angsuranQuery(Request $request)
    {
        $query = Angsuran::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.marketing.user',
        ]);

        $this->applyScope($query, 'pembiayaan.pengajuan');

        $this->filterTanggal($query, $request, 'tanggal_jatuh_tempo');

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
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_angsuran', 'like', "%{$keyword}%");
                $q->orWhereHas('pembiayaan', function ($sub) use ($keyword) {
                    $sub->where('nomor_pembiayaan', 'like', "%{$keyword}%");
                });
                $q->orWhereHas('pembiayaan.pengajuan.nasabah', function ($sub) use ($keyword) {
                    $sub->where('nama', 'like', "%{$keyword}%");
                });
            });
        }
        return $query;
    }

    public function pelunasanQuery(Request $request)
    {
        $query = Pelunasan::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.marketing.user',
        ]);

        $this->applyScope($query, 'pembiayaan.pengajuan');

        $this->filterTanggal($query, $request, 'tanggal_pelunasan');

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
                $q->where('nomor_pelunasan', 'like', "%{$keyword}%");
                $q->orWhereHas('pembiayaan', function ($sub) use ($keyword) {
                    $sub->where('nomor_pembiayaan', 'like', "%{$keyword}%");
                });
                $q->orWhereHas('pembiayaan.pengajuan.nasabah', function ($sub) use ($keyword) {
                    $sub->where('nama', 'like', "%{$keyword}%");
                });
            });
        }
        return $query;
    }

    public function outstandingQuery(Request $request)
    {
        $query = Pembiayaan::with([
            'pengajuan.nasabah',
            'pengajuan.cabang',
            'pengajuan.marketing.user',
            'angsurans',
        ]);

        $this->applyScope($query);

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

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_pembiayaan', 'like', "%{$keyword}%");
                $q->orWhereHas('pengajuan.nasabah', function ($sub) use ($keyword) {
                    $sub->where('nama', 'like', "%{$keyword}%");
                });
            });
        }

        $query->whereHas('angsurans', function ($q) {
            $q->where('sisa_tagihan', '>', 0);
        });

        return $query;
    }

    public function jatuhTempoQuery(Request $request)
    {
        $query = Angsuran::with([
            'pembiayaan.pengajuan.nasabah',
            'pembiayaan.pengajuan.cabang',
            'pembiayaan.pengajuan.marketing.user',
        ]);
    
        $this->applyScope($query, 'pembiayaan.pengajuan');
    
        $this->filterTanggal($query, $request, 'tanggal_jatuh_tempo');
    
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
    
                $q->whereHas('pembiayaan', function ($sub) use ($keyword) {
    
                    $sub->where('nomor_pembiayaan', 'like', "%{$keyword}%");
    
                });
    
                $q->orWhereHas('pembiayaan.pengajuan.nasabah', function ($sub) use ($keyword) {
    
                    $sub->where('nama', 'like', "%{$keyword}%");
    
                });
    
            });
    
        }
    
        return $query;
    }
     
    public function nplQuery(Request $request)
    {
        return $this->jatuhTempoQuery($request)
            ->where('status', 'jatuh_tempo');
    }
}