<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Kelompok;
use App\Models\Murojaah;
use App\Models\Absensi_ustadz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MurojaahController extends Controller
{
    public function index()
    {
        $user = auth()->user();
    
        // base query
        $santrisQuery = Santri::with(['kelompok.ustadz.user','kelasnya']);
        $kelompoksQuery = Kelompok::query()->orderBy('nama_kelompok');
    
        // ================= 1. ADMIN =================
        if ($user->hasRole('admin')) {
    
            // tidak ada filter ekstra:
            // - semua santri
            // - semua kelompok (tilawah & btq)
        // ================= 4. USTADZ =================
        } elseif ($user->hasRole('ustadz')) {
    
            $ustadz = $user->ustadz;   // relasi di model User: hasOne(Ustadz::class)
            if (!$ustadz) {
                abort(403, 'Data ustadz tidak ditemukan.');
            }
    
            $kelompok = $ustadz->kelompok; // relasi di model Ustadz: belongsTo(Kelompok::class)
            if (!$kelompok) {
                abort(403, 'Kelompok ustadz tidak ditemukan.');
            }
    
    
            // gender santri ditentukan oleh jenis kelompok
            $gender = $kelompok->jenis === 'putra'
                ? 'laki-laki'
                : 'perempuan';
    
            // untuk ustadz, kelompok yang ditampilkan cukup kelompok miliknya saja
            $kelompoksQuery->where('id', $kelompok->id);
    
            // santri:
            $santrisQuery
                // ->where('kelompok_id', $kelompok->id)
                ->where('kelamin', $gender);
        } else {
            // role tidak dikenali
            abort(403, 'Anda tidak memiliki akses.');
        }
    
        $santris    = $santrisQuery->get();
        $kelompoks = $kelompoksQuery->get();
    
        return view('murojaahs.index', compact('santris', 'kelompoks'));
    }
    
    public function create()
    {
        $user = auth()->user();

        // base query kelompok tilawah
        $kelompoksQuery = Kelompok::select('id','nama_kelompok','jenis');

        // ========== ROLE ADMIN ==========
        if ($user->hasRole('admin')) {
            // admin melihat seluruh kelompok → tidak ada filter jenis
            // jadi tidak perlu tambah where

        // ========== ROLE USTADZ ==========
        } elseif ($user->hasRole('ustadz')) {

            $ustadz = $user->ustadz;

            if ($ustadz) {

                // ustadz laki-laki → kelompok putra
                // ustadz perempuan → kelompok putri
                $jenis = $ustadz->kelamin === 'laki-laki' ? 'putra' : 'putri';

                $kelompoksQuery->where('jenis', $jenis);
            }

        } else {
            // role selain ustadz atau admin → tampilkan semua kelompok tilawah
            // tanpa filter jenis
        }

        $kelompoks = $kelompoksQuery
            ->orderBy('nama_kelompok')
            ->get();

        // dropdown surat (nama & nomor) — cukup yang dibutuhkan
        $surat = DB::table('madina')
            ->select('sura_no','sura_name', DB::raw('MIN(id) as id'))
            ->groupBy('sura_no','sura_name')
            ->orderBy('sura_no')
            ->get();

        return view('murojaahs.create', compact('kelompoks','surat'));
    }

     // AJAX: ambil Santri pada kelompok tertentu
     public function getSantrisByKelompok($kelompokId)
     {
         $santris = Santri::where('kelompok_id', $kelompokId)
             ->with('user') // pastikan relasi dengan User untuk mendapatkan 'name'
             ->get();
     
         return response()->json($santris->map(function ($santri) {
             return [
                 'id' => $santri->id,
                 'name' => $santri->user->name,  // Ambil nama santri dari relasi User
             ];
         }));
     }     

      // AJAX (opsional): detail surat untuk isi No/Juz/Halaman otomatis
    public function getSuratDetails($sura_no)
    {
        $head = DB::table('madina')->where('sura_no',$sura_no)->orderBy('page')->first();
        $pages = DB::table('madina')
            ->where('sura_no',$sura_no)
            ->selectRaw('MIN(page) as start_page, MAX(page) as end_page')
            ->first();

        if(!$head || !$pages){
            return response()->json(['status'=>'error','message'=>'Surat tidak ditemukan'],404);
        }

        return response()->json([
            'status'=>'success',
            'data'=>[
                'no_surat'   => $head->sura_no,
                'sura_name'  => $head->sura_name,
                'jozz'       => $head->jozz ?? ($head->juz ?? null),
                'start_page' => $pages->start_page,
                'end_page'   => $pages->end_page,
            ]
        ]);
    }

   public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tgl' => 'required|date|before_or_equal:today',
            'kelompok_id' => 'required|exists:kelompoks,id', // Validasi kelompok_id
            'santri_id' => 'required|exists:santris,id', // Validasi santri_id
            'surat_no' => 'required|exists:madina,sura_no', // Validasi surat_no
            'dariayat' => 'required|integer|min:1',
            'sampaiayat' => 'required|integer|gte:dariayat',
            'keterangan' => 'nullable|string',
        ]);

        // Ambil data berdasarkan input
        $santri = Santri::findOrFail($request->santri_id);
        $kelompok = Kelompok::findOrFail($request->kelompok_id);
        $surat = DB::table('madina')->where('sura_no', $request->surat_no)->first();

        // Cek apakah santri sudah tercatat pada kelompok yang sesuai
        if ($santri->kelompok_id !== $kelompok->id) {
            return back()->with('error', 'Santri ini tidak termasuk dalam kelompok yang dipilih.');
        }

        // Ambil ustadz yang sedang login
        $ustadz = auth()->user()->ustadz;

        // Cek apakah ustadz sudah melakukan absensi di tanggal yang sama
        $absensiUstadz = Absensi_ustadz::where('ustadz_id', $ustadz->id)
                                        ->whereDate('tgl_absen', $request->tgl)
                                        ->first();

        // Jika absensi sudah ada untuk tanggal tersebut, kita izinkan melanjutkan store
        // Tidak perlu error, karena ustadz bisa mengabsen santri yang lain pada tanggal yang sama
        if (!$absensiUstadz) {
            // Jika belum ada, maka simpan absensi untuk ustadz
            Absensi_ustadz::create([
                'ustadz_id' => $ustadz->id,
                'tgl_absen' => $request->tgl,
                'status' => 'hadir', // Status absensi untuk ustadz
                'keterangan' => 'absen dari murojaah',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $santri, $surat, $kelompok, $ustadz) {
                // Simpan data ke Murojaah
                Murojaah::create([
                    'santri_id' => $santri->id,
                    'ustadz_id' => $ustadz->id, // Ambil id ustadz yang sedang login
                    'surat_id' => $surat->id,
                    'surat_no' => $surat->sura_no,
                    'dariayat' => $request->dariayat,
                    'sampaiayat' => $request->sampaiayat,
                    'tgl_murojaah' => $request->tgl,
                    'keterangan' => $request->keterangan ?? null,
                ]);
            });

            return redirect()
                ->route('murojaahs.show', $santri->id)
                ->with('success', 'Data Murojaah berhasil disimpan dan Absensi Ustadz tercatat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }


    public function getMurojaahBySantri($santri_id)
    {
        $histories = DB::table('murojaahs')
            ->join('madina', 'murojaahs.surat_id', '=', 'madina.id')
            ->where('murojaahs.santri_id', $santri_id)
            ->select(
                'murojaahs.id',
                'murojaahs.tgl_murojaah',
                'madina.sura_name',
                'murojaahs.dariayat',
                'murojaahs.sampaiayat',
                'murojaahs.keterangan'
            )
            ->orderByDesc('murojaahs.tgl_murojaah')
            ->limit(10) // tampilkan 10 terakhir
            ->get();

        return response()->json($histories);
    }

    public function show($id)
    {
        $santri = Santri::with([
            'kelompok',
            'kelasnya',
            'ustadz.user'
        ])->findOrFail($id);

        // Ambil histori murojaah berdasarkan santri_id, urutkan berdasarkan tanggal
        $murojaahHistories = Murojaah::where('santri_id', $id)
            ->orderBy('tgl_murojaah', 'desc') // Urutkan berdasarkan tanggal murojaah
            ->get();
        
        // Kembalikan data ke view 'murojaah.show'
        return view('murojaahs.show', compact('santri', 'murojaahHistories'));
    }
    
    public function edit($id)
    {
        $user = auth()->user();

        // base query kelompok tilawah
        $kelompoksQuery = Kelompok::select('id','nama_kelompok','jenis');

        // ========== ROLE ADMIN ==========
        if ($user->hasRole('admin')) {
            // admin melihat seluruh kelompok → tidak ada filter jenis
            // jadi tidak perlu tambah where

        // ========== ROLE USTADZ ==========
        } elseif ($user->hasRole('ustadz')) {

            $ustadz = $user->ustadz;

            if ($ustadz) {

                // ustadz laki-laki → kelompok putra
                // ustadz perempuan → kelompok putri
                $jenis = $ustadz->kelamin === 'laki-laki' ? 'putra' : 'putri';

                $kelompoksQuery->where('jenis', $jenis);
            }

        // ========== ROLE LAIN (adminputra, adminputri) ==========
        } else {
            // role selain ustadz atau admin → tampilkan semua kelompok tilawah
            // tanpa filter jenis
        }

        $kelompoks = $kelompoksQuery
            ->orderBy('nama_kelompok')
            ->get();

        // Ambil data tadarus berdasarkan ID
        $murojaah = Murojaah::findOrFail($id);
        // dd($murojaahs);
        // $kelompoks = Kelompok::all();  // Ambil semua kelompok untuk dropdown
        
        $surat = DB::table('madina')
        ->select('sura_no','sura_name', DB::raw('MIN(id) as id'))
        ->groupBy('sura_no','sura_name')
        ->orderBy('sura_no')
        ->get();
    
        
        // Kirim data ke view edit
        return view('murojaahs.edit', compact('murojaah', 'kelompoks', 'surat'));
    }

    public function update(Request $request, $id)
    {
        // ======================
        // VALIDASI
        // ======================
        $validated = $request->validate([
            'tgl_murojaah'=> 'required|date',
            'kelompok_id' => 'required|exists:kelompoks,id',
            'santri_id'   => 'required|exists:santris,id',
            'surat_no'    => 'required|exists:madina,sura_no',
            'dariayat'    => 'required|integer|min:1',
            'sampaiayat'  => 'required|integer|min:1|gte:dariayat',
            'keterangan'  => 'required|string',
        ]);
    
        // ======================
        // AMBIL DATA MUROJAAH
        // ======================
        $murojaah = Murojaah::findOrFail($id);
        $user = auth()->user();
    
        // ======================
        // BATASI AKSES USTADZ
        // ======================
        if ($user->hasRole('ustadz')) {
            $ustadz = $user->ustadz;
    
            if (!$ustadz || $murojaah->ustadz_id !== $ustadz->id) {
                abort(403, 'Anda tidak berhak mengubah data ini.');
            }
        }
    
        // ======================
        // AMBIL SURAT (MADINA)
        // ======================
        $surat = DB::table('madina')
            ->where('sura_no', $validated['surat_no'])
            ->orderBy('id')
            ->first();
    
        if (!$surat) {
            return back()->with('error', 'Data surat tidak ditemukan.');
        }
    
        // ======================
        // UPDATE DATA
        // ======================
        DB::transaction(function () use ($murojaah, $validated, $surat) {
    
            $murojaah->update([
                'santri_id'    => $validated['santri_id'],
                'surat_id'     => $surat->id,
                'surat_no'     => $validated['surat_no'],
                'dariayat'     => $validated['dariayat'],
                'sampaiayat'   => $validated['sampaiayat'],
                'tgl_murojaah' => $validated['tgl_murojaah'],
                'keterangan'   => $validated['keterangan'],
            ]);
        });
    
        return redirect()
            ->route('murojaahs.show', $validated['santri_id'])
            ->with('success', 'Data murojaah berhasil diperbarui.');
    }

    public function rekap(Request $request)
    {
        $user = auth()->user();
    
        // ============================
        // DEFAULT TANGGAL
        // ============================
        $defaultStart = Carbon::now()->startOfMonth()->toDateString();
        $defaultEnd   = Carbon::now()->toDateString();
    
        $nama       = $request->nama_santri;
        $kelompokId = $request->kelompok_id;
    
        $startDate  = $request->filled('start_date') ? $request->start_date : $defaultStart;
        $endDate    = $request->filled('end_date')   ? $request->end_date   : $defaultEnd;
    
        // dropdown kelompok
        $kelompoks = Kelompok::orderBy('nama_kelompok')->get();
    
        // ============================
        // QUERY MUROJAAH
        // ============================
        $data = Murojaah::query()
            ->with([
                'santri.user',
                'santri.kelompok',
                'santri.kelasnya',
                'surat',
            ])
    
            // 🔹 filter nama santri
            ->when($nama, function ($q) use ($nama) {
                $q->whereHas('santri.user', function ($qq) use ($nama) {
                    $qq->where('name', 'like', "%{$nama}%");
                });
            })
    
            // 🔹 filter kelompok
            ->when($kelompokId, function ($q) use ($kelompokId) {
                $q->whereHas('santri', function ($qq) use ($kelompokId) {
                    $qq->where('kelompok_id', $kelompokId);
                });
            })
    
            // 🔹 filter ustadz (jika login ustadz)
            ->when($user->hasRole('ustadz'), function ($q) use ($user) {
                $q->where('ustadz_id', $user->ustadz->id);
            })
    
            // 🔹 filter tanggal
            ->whereBetween('tgl_murojaah', [$startDate, $endDate])
    
            ->orderBy('tgl_murojaah', 'desc')
            ->get();
    
        return view('murojaahs.rekap', [
            'data'       => $data,
            'kelompoks'  => $kelompoks,
            'filters'    => compact('nama', 'kelompokId', 'startDate', 'endDate'),
        ]);
    }
    
}
