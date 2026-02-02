<?php

namespace App\Http\Controllers;
use App\Models\Santri;
use App\Models\Ustadz;
use App\Models\Kelasnya;
use App\Models\Kelompok;
use App\Models\Murojaah;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
 
    // Dashboard untuk Admin
    public function indexadmin()
    {
        $data['totalSantriPutra'] = Santri::where('kelamin', 'laki-laki')->count();

        // 🔹 TOTAL Santri PEREMPUAN
        $data['totalSantriPutri'] = Santri::where('kelamin', 'perempuan')->count();

        // 🔹 TOTAL USTADZ LAKI-LAKI
        $data['totalUstadzPutra'] = Ustadz::where('kelamin', 'laki-laki')->count();

        // 🔹 TOTAL USTADZ PEREMPUAN
        $data['totalUstadzPutri'] = Ustadz::where('kelamin', 'perempuan')->count();
        $data['kelompokPutra'] = Kelompok::where('jenis', 'putra')->count();
        $data['kelompokPutri'] = Kelompok::where('jenis', 'putri')->count();
        $data['totalKelas'] = Kelasnya::count();

      /*   $santrisQuery = Santri::with(['kelompok.ustadz.user','kelasnya']);
        $data['santris']    = $santrisQuery->limit(5)->get(); */

        $data['latestMurojaah'] = Murojaah::with(['santri', 'ustadz','user'])
        ->orderBy('tgl_murojaah', 'desc')->limit(5)->get();
        // dd($data['latestMurojaah']);
        return view('dashboard.admin',$data);  // Halaman dashboard admin
    }

    // Dashboard untuk Ustadz
    public function indexustadz()
    {
        $data['totalSantriPutra'] = Santri::where('kelamin', 'laki-laki')->count();

        // 🔹 TOTAL Santri PEREMPUAN
        $data['totalSantriPutri'] = Santri::where('kelamin', 'perempuan')->count();

        // 🔹 TOTAL USTADZ LAKI-LAKI
        $data['totalUstadzPutra'] = Ustadz::where('kelamin', 'laki-laki')->count();

        // 🔹 TOTAL USTADZ PEREMPUAN
        $data['totalUstadzPutri'] = Ustadz::where('kelamin', 'perempuan')->count();
        $data['kelompokPutra'] = Kelompok::where('jenis', 'putra')->count();
        $data['kelompokPutri'] = Kelompok::where('jenis', 'putri')->count();
        $data['totalKelas'] = Kelasnya::count();

        $data['latestMurojaah'] = Murojaah::with(['santri', 'ustadz', 'user'])
        ->where('ustadz_id', auth()->user()->ustadz->id) // Filter berdasarkan ustadz yang login
        ->orderBy('tgl_murojaah', 'desc')
        ->limit(5)
        ->get();

        return view('dashboard.ustadz', $data);  // Halaman dashboard ustadz
    }

    // Dashboard untuk Santri
    public function indexSantri()
    {
        $data['detail'] = Santri::with(['kelompok','kelasnya','ustadz.user'])->findOrFail(auth()->user()->santri->id);

        $data['latestMurojaah'] = Murojaah::with(['santri', 'ustadz', 'user'])
        ->where('santri_id', auth()->user()->santri->id) // Filter berdasarkan ustadz yang login
        ->orderBy('tgl_murojaah', 'desc')
        ->limit(5)
        ->get();

        return view('dashboard.santri', $data);  // Halaman dashboard Santri
    }
}
