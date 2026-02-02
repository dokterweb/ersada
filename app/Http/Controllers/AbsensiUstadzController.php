<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ustadz;
use App\Models\Harilibur;
use Illuminate\Http\Request;
use App\Models\Absensi_ustadz;

class AbsensiUstadzController extends Controller
{
    public function index(Request $request)
    {
        // Menentukan tanggal mulai dan tanggal selesai
        $tanggalMulai = $request->has('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->has('tanggal_selesai') ? Carbon::parse($request->tanggal_selesai) : Carbon::now();
    
        // Filter absensi berdasarkan tanggal
        $absensiUstadz = Absensi_ustadz::with('ustadz')
            ->whereBetween('tgl_absen', [$tanggalMulai, $tanggalSelesai])->get();
    
        return view('absensiustadzs.index', compact('absensiUstadz', 'tanggalMulai', 'tanggalSelesai'));
    }

    public function edit($id)
    {
        $absensi = Absensi_ustadz::with('ustadz.user')->findOrFail($id);

        return response()->json($absensi);
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'tgl_absen'     => 'required|date',
            'status'        => 'required|in:hadir,ghoib,sakit,izin',
            'keterangan'    => 'nullable|string',
        ]);

        // Cek apakah tanggal absen adalah hari libur
        $tglAbsensi = $validated['tgl_absen'];
        $hariLibur = Harilibur::whereDate('tanggal_mulai', '<=', $tglAbsensi)
                            ->whereDate('tanggal_selesai', '>=', $tglAbsensi)
                            ->exists();

        // Jika tanggal adalah hari libur, tampilkan notifikasi dan batalkan update
        if ($hariLibur) {
            return redirect()->route('absensiustadzs.index')
                ->with('error', 'Tanggal tersebut adalah hari libur!'); // Notifikasi error
        }

        // Update data absensi
        $absensi = Absensi_ustadz::findOrFail($id);
        $absensi->update([
            'tgl_absen'     => $validated['tgl_absen'],
            'status'        => $validated['status'],
            'keterangan'    => $validated['keterangan'],
        ]);

        // Redirect setelah update
        return redirect()->route('absensiustadzs.index')->with('success', 'Absensi berhasil diupdate');
    }

    public function rekapabsenustadz(Request $request)
{
    // Ambil santri_id dari user yang login
    $ustadzId = auth()->user()->ustadz->id; // Ambil santri_id dari user yang login
    
    // Debugging untuk memastikan $ustadzId ada
    // dd($ustadzId); // Pastikan ini mengeluarkan ID yang benar

    // Default: tanggal 1 bulan ini sampai hari ini
    $defaultStart = Carbon::now()->startOfMonth()->toDateString();
    $defaultEnd   = Carbon::now()->toDateString();

    // Kalau user isi form pakai nilai form, kalau tidak pakai default
    $startDate  = $request->filled('start_date') ? $request->start_date : $defaultStart;
    $endDate    = $request->filled('end_date')   ? $request->end_date   : $defaultEnd;

    // Ambil nama tabel dari model (supaya tidak salah)
    $tblUstadz   = (new Ustadz)->getTable();          // "Santris"
    $tblAbsensi = (new Absensi_ustadz)->getTable();   // "absensi_Santris"
    
    // Query rekap absensi untuk santri yang login
    $rekap = Ustadz::query()
        ->with(['kelompok', 'user']) // Pastikan relasi 'user' ada di sini
        ->where('id', $ustadzId)  // Filter berdasarkan santri yang login
        ->select("{$tblUstadz}.*")   // Menambahkan filter berdasarkan santri
        ->addSelect([
            'hadir' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblUstadz, $ustadzId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "hadir" THEN 1 END) as hadir')
                    ->whereColumn("{$tblAbsensi}.ustadz_id", "{$tblUstadz}.id")
                    ->where('ustadz_id', $ustadzId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
            'ghoib' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblUstadz, $ustadzId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "ghoib" THEN 1 END) as ghoib')
                    ->whereColumn("{$tblAbsensi}.ustadz_id", "{$tblUstadz}.id")
                    ->where('ustadz_id', $ustadzId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
            'izin' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblUstadz, $ustadzId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "izin" THEN 1 END) as izin')
                    ->whereColumn("{$tblAbsensi}.ustadz_id", "{$tblUstadz}.id")
                    ->where('ustadz_id', $ustadzId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
            'sakit' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblUstadz, $ustadzId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "sakit" THEN 1 END) as sakit')
                    ->whereColumn("{$tblAbsensi}.ustadz_id", "{$tblUstadz}.id")
                    ->where('ustadz_id', $ustadzId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
            'pulang' => function ($q) use ($startDate, $endDate, $tblAbsensi, $tblUstadz, $ustadzId) {
                $q->from($tblAbsensi)
                    ->selectRaw('COUNT(CASE WHEN status = "pulang" THEN 1 END) as pulang')
                    ->whereColumn("{$tblAbsensi}.ustadz_id", "{$tblUstadz}.id")
                    ->where('ustadz_id', $ustadzId) // Filter berdasarkan santri yang login
                    ->when($startDate, fn ($qq) => $qq->whereDate('tgl_absen', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('tgl_absen', '<=', $endDate));
            },
        ])
        ->get()
        ->map(function ($ustadz) {
            $hadir = $ustadz->hadir;
            $total = max($ustadz->total_hari, 1); // Hindari pembagian dengan nol
            $ustadz->persen_hadir = round($hadir / $total * 100);
            return $ustadz;
        });

    return view('absensiustadzs.rekapustadz', [
        'rekap'      => $rekap,
        'filters'    => compact('startDate', 'endDate'),
    ]);
}

}
