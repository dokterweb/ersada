<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Pembiayaan;
use App\Models\Pencairan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportService
{
    public function applyScope($query)
    {
        $user = auth()->user();

        // Marketing
        if ($user->hasRole('marketing')) {
            $marketingId = $user->karyawan?->id;
            $query->whereHas('pengajuan', function ($q) use ($marketingId) {
                $q->where('marketing_id', $marketingId);
            });
        }

        // Admin Cabang
        elseif ($user->hasRole('admin')) {
            $cabangId = $user->karyawan?->cabang_id;
            $query->whereHas('pengajuan', function ($q) use ($cabangId) {
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
            'akad.pembiayaan.pengajuan.nasabah',
            'akad.pembiayaan.pengajuan.cabang',
            'akad.pembiayaan.pengajuan.marketing.user',
        ]);

        $this->applyScope($query);

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
}