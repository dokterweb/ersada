<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Kelompok;
use App\Models\Harilibur;
use Illuminate\Http\Request;
use App\Models\Absensi_santri;

class AbsensiSantriController extends Controller
{

    public function index(Request $request)
    {
        // Menentukan tanggal mulai dan tanggal selesai
        $tanggalMulai = $request->has('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->has('tanggal_selesai') ? Carbon::parse($request->tanggal_selesai) : Carbon::now();
    
        // Filter absensi berdasarkan tanggal
        $absensiSantris = Absensi_santri::with('santri')
            ->whereBetween('tgl_absen', [$tanggalMulai, $tanggalSelesai])->get();
    
        return view('absensisantris.index', compact('absensiSantris', 'tanggalMulai', 'tanggalSelesai'));
    }

    public function create()
    {
        $user = auth()->user();

        // ADMIN
        if ($user->hasRole('admin')) {
            $kelompoks = Kelompok::all();
            return view('absensisantris.create', compact('kelompoks'));
        }

        // USTADZ
        if ($user->hasRole('ustadz')) {
            $kelompok = $user->ustadz->kelompok; // Ambil kelompok dari ustadz yang login
            $santris = $kelompok->santris()->get(); // Ambil santri di kelompok tersebut
            return view('absensisantris.create', compact('kelompok', 'santris'));
        }

        abort(403);
    }


    public function getSantri(Kelompok $kelompok)
    {
        // keamanan ustadz
        if (auth()->user()->hasRole('ustadz')) {
            abort_if($kelompok->id !== auth()->user()->ustadz->kelompok_id, 403);
        }

        return response()->json(
            $kelompok->santris()->with('user')->get()
        );
    }


    public function store(Request $request)
    {
        // Cek apakah tanggal absensi ada di tabel hari libur
        $tglAbsensi = $request->tgl_absen;
        $hariLibur = Harilibur::whereDate('tanggal_mulai', '<=', $tglAbsensi)
                            ->whereDate('tanggal_selesai', '>=', $tglAbsensi)
                            ->exists();

        // Jika tanggal adalah hari libur, tampilkan notifikasi dan batalkan penyimpanan
        if ($hariLibur) {
            return redirect()->route('absensisantris.index')
                ->with('error', 'Tanggal tersebut adalah hari libur!'); // Notifikasi error
        }

        // Jika bukan hari libur, simpan data absensi
        foreach ($request->absensi as $santriId => $data) {
            // Ambil status dan keterangan per santri
            $status     = $data['status'];
            $keterangan = $data['keterangan'] ?? null; // Keterangan bisa null jika tidak diisi

            // Simpan data absensi
            Absensi_santri::create([
                'tgl_absen'     => $tglAbsensi,
                'santri_id'     => $santriId,
                'status'        => $status,
                'keterangan'    => $keterangan,
            ]);
        }

        // Redirect ke halaman absensi setelah disimpan
        return redirect()
            ->route('absensisantris.index')
            ->with('success', 'Absensi berhasil disimpan');
    }

    
    public function edit($id)
    {
        $absensi = Absensi_santri::with('santri.user')->findOrFail($id);

        return response()->json($absensi);
    }
    
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'tgl_absen' => 'required|date',
            'status' => 'required|in:hadir,ghoib,sakit,izin',
            'keterangan' => 'nullable|string',
        ]);

        // Cek apakah tanggal absen adalah hari libur
        $tglAbsensi = $validated['tgl_absen'];
        $hariLibur = Harilibur::whereDate('tanggal_mulai', '<=', $tglAbsensi)
                            ->whereDate('tanggal_selesai', '>=', $tglAbsensi)
                            ->exists();

        // Jika tanggal adalah hari libur, tampilkan notifikasi dan batalkan update
        if ($hariLibur) {
            return redirect()->route('absensisantris.index')
                ->with('error', 'Tanggal tersebut adalah hari libur!'); // Notifikasi error
        }

        // Update data absensi
        $absensi = Absensi_santri::findOrFail($id);
        $absensi->update([
            'tgl_absen' => $validated['tgl_absen'],
            'status' => $validated['status'],
            'keterangan' => $validated['keterangan'],
        ]);

        // Redirect setelah update
        return redirect()->route('absensisantris.index')->with('success', 'Absensi berhasil diupdate');
    }
    
    public function rekapabsen(Request $request)
    {
        // ambil filter dari request
        $nama       = $request->nama_santri; // Pastikan `nama_santri` adalah parameter yang benar di form
        $kelompokId = $request->kelompok_id;
    
        // default: tanggal 1 bulan ini sampai hari ini
        $defaultStart = Carbon::now()->startOfMonth()->toDateString();
        $defaultEnd   = Carbon::now()->toDateString();
    
        // kalau user isi form pakai nilai form, kalau tidak pakai default
        $startDate  = $request->filled('start_date') ? $request->start_date : $defaultStart;
        $endDate    = $request->filled('end_date')   ? $request->end_date   : $defaultEnd;
        
        // untuk dropdown kelompok
        $kelompoks = Kelompok::orderBy('nama_kelompok')->get();
    
        // ambil nama tabel dari model (supaya tidak salah)
        $tblSantri   = (new Santri)->getTable();          // "Santris"
        $tblAbsensi = (new Absensi_santri)->getTable();   // "absensi_Santris"
        
        // query rekap
        $rekap = Santri::query()
            ->with(['kelompok', 'kelasnya', 'user']) // Pastikan relasi 'user' ada di sini
            ->when($nama, function ($q) use ($nama) {
                // Cari berdasarkan nama santri yang ada di tabel `users`
                $q->whereHas('user', function ($query) use ($nama) {
                    $query->where('name', 'like', "%{$nama}%"); // Pencarian berdasarkan nama user
                });
            })
            ->when($kelompokId, function ($q) use ($kelompokId) {
                $q->where('kelompok_id', $kelompokId);
            })
            ->select("{$tblSantri}.*")   // Menambahkan filter berdasarkan santri
            ->addSelect([
                // Hitung hadir, ghoib, izin, sakit, pulang dalam satu query menggunakan CASE WHEN
                'hadir' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri) {
                    $q->from($tblAbsensi)
                        ->selectRaw('COUNT(CASE WHEN status = "hadir" THEN 1 END) as hadir')
                        ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                        ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                        ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
                },
                'ghoib' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri) {
                    $q->from($tblAbsensi)
                        ->selectRaw('COUNT(CASE WHEN status = "ghoib" THEN 1 END) as ghoib')
                        ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                        ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                        ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
                },
                'izin' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri) {
                    $q->from($tblAbsensi)
                        ->selectRaw('COUNT(CASE WHEN status = "izin" THEN 1 END) as izin')
                        ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                        ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                        ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
                },
                'sakit' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri) {
                    $q->from($tblAbsensi)
                        ->selectRaw('COUNT(CASE WHEN status = "sakit" THEN 1 END) as sakit')
                        ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                        ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                        ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
                },
                'pulang' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri) {
                    $q->from($tblAbsensi)
                        ->selectRaw('COUNT(CASE WHEN status = "pulang" THEN 1 END) as pulang')
                        ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                        ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                        ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
                },
            ])
            ->get()
            ->map(function ($santri) {
                $hadir = $santri->hadir;
                $total = max($santri->total_hari, 1); // Hindari pembagian dengan nol
                $santri->persen_hadir = round($hadir / $total * 100);
                return $santri;
            });
    
        return view('absensisantris.rekap', [
            'rekap'      => $rekap,
            'kelompoks'  => $kelompoks,
            'filters'    => compact('nama', 'kelompokId', 'startDate', 'endDate'),
        ]);
    }
    
    public function rekapabsensantri(Request $request)
{
    // Ambil santri_id dari user yang login
    $santriId = auth()->user()->santri->id; // Ambil santri_id dari user yang login
    
    // Debugging untuk memastikan $santriId ada
    // dd($santriId); // Pastikan ini mengeluarkan ID yang benar

    // Default: tanggal 1 bulan ini sampai hari ini
    $defaultStart = Carbon::now()->startOfMonth()->toDateString();
    $defaultEnd   = Carbon::now()->toDateString();

    // Kalau user isi form pakai nilai form, kalau tidak pakai default
    $startDate  = $request->filled('start_date') ? $request->start_date : $defaultStart;
    $endDate    = $request->filled('end_date')   ? $request->end_date   : $defaultEnd;

    // Ambil nama tabel dari model (supaya tidak salah)
    $tblSantri   = (new Santri)->getTable();          // "Santris"
    $tblAbsensi = (new Absensi_santri)->getTable();   // "absensi_Santris"
    
    // Query rekap absensi untuk santri yang login
    $rekap = Santri::query()
        ->with(['kelompok', 'kelasnya', 'user']) // Pastikan relasi 'user' ada di sini
        ->where('id', $santriId)  // Filter berdasarkan santri yang login
        ->select("{$tblSantri}.*")   // Menambahkan filter berdasarkan santri
        ->addSelect([
            'hadir' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri, $santriId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "hadir" THEN 1 END) as hadir')
                    ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                    ->where('santri_id', $santriId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
            'ghoib' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri, $santriId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "ghoib" THEN 1 END) as ghoib')
                    ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                    ->where('santri_id', $santriId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
            'izin' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri, $santriId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "izin" THEN 1 END) as izin')
                    ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                    ->where('santri_id', $santriId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
            'sakit' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri, $santriId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "sakit" THEN 1 END) as sakit')
                    ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                    ->where('santri_id', $santriId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
            'pulang' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblSantri, $santriId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "pulang" THEN 1 END) as pulang')
                    ->whereColumn("{$tblAbsensi}.santri_id", "{$tblSantri}.id")
                    ->where('santri_id', $santriId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
        ])
        ->get()
        ->map(function ($santri) {
            $hadir = $santri->hadir;
            $total = max($santri->total_hari, 1); // Hindari pembagian dengan nol
            $santri->persen_hadir = round($hadir / $total * 100);
            return $santri;
        });

    return view('absensisantris.rekapsantri', [
        'rekap'      => $rekap,
        'filters'    => compact('startDate', 'endDate'),
    ]);
}

    
}
