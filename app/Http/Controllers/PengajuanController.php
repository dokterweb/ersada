<?php

namespace App\Http\Controllers;


use App\Http\Requests\PengajuanStep1Request;
use App\Http\Requests\PengajuanStep3Request;
use App\Models\AnalisaPengajuan;
use App\Models\ApprovalPengajuan;
use App\Models\Dokumen_payroll;
use App\Models\Dokumen_pengajuan;
use App\Models\DokumenJaminan;
use App\Models\JaminanPengajuan;
use App\Models\KapitalPengajuan;
use App\Models\Karyawan;
use App\Models\Nasabah;
use App\Models\Pekerjaan_nasabah;
use App\Models\Pekerjaan_referensi;
use App\Models\Pengajuan;
use App\Models\Referensi;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PengajuanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        if (!$karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        $query = Pengajuan::with(['nasabah','marketing.user','cabang',]);

        /*
        * Direktur bisa melihat semua cabang
        */
        if (!$user->hasRole('direktur')) {
            $query->where('cabang_id', auth()->user()->getCabangId());
        }

        $pengajuans = $query->latest()->paginate(15);

        return view('pengajuans.index',compact('pengajuans'));
    }

    public function createStep1()
    {
        $marketingOptions = $this->getMarketingOptionsForUser();

        return view(
            'pengajuans.step1',
            [
                'pengajuan' => null,
                'marketingOptions' => $marketingOptions,
            ]
        );
    }

    public function storeStep1(PengajuanStep1Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $karyawanUser = $user->karyawan;

            if (!$karyawanUser) {
                abort(403, 'Data karyawan tidak ditemukan.');
            }

            if ($user->hasRole('admincabang')) {

                if (!$karyawanUser->cabang_id) {
                    abort(403, 'Cabang pengguna belum ditentukan.');
                }

                $marketing = Karyawan::query()
                    ->where('id', $request->marketing_id)
                    ->where('cabang_id',$karyawanUser->cabang_id)
                    ->whereHas('user', function ($query) {
                        $query->role([
                            'marketing',
                            'spvmarketing'
                        ]);
                    })
                    ->first();

                if (!$marketing) {
                    abort(
                        403,
                        'Marketing yang dipilih tidak valid atau bukan berasal dari cabang Anda.'
                    );
                }

                $cabangId = $marketing->cabang_id;
                $marketingId = $marketing->id;
            } else {
                if (
                    !$user->hasAnyRole(['marketing','spvmarketing'])
                ) {
                    abort(403,'Anda tidak memiliki hak untuk membuat pengajuan.');
                }

                if (!$karyawanUser->cabang_id) {
                    abort(403, 'Cabang pengguna belum ditentukan.');
                }

                $cabangId = $karyawanUser->cabang_id;
                $marketingId = $karyawanUser->id;
            }

            $nomor = 'PGJ-' . now()->format('YmdHis');
            $pengajuan = Pengajuan::create([
                'nomor_pengajuan' =>$nomor,
                'cabang_id' =>$cabangId,
                'marketing_id' =>$marketingId,
                'status' =>'draft',
                'current_step' =>2,
                'tanggal_pengajuan' =>$request->tanggal_pengajuan,
                'nominal_pengajuan' =>$request->nominal_pengajuan,
                'tenor' =>$request->tenor,
                'kategori_nasabah' =>$request->kategori_nasabah,
                'status_customer' =>$request->status_customer,
                'tujuan_pinjaman' =>$request->tujuan_pinjaman,
                'catatan' =>$request->catatan,
            ]);

            DB::commit();

            return redirect()->route('pengajuan.step2',$pengajuan->id)->with('success','Step 1 berhasil disimpan');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    public function editStep1(Pengajuan $pengajuan)
    {
        if (!in_array($pengajuan->status, ['draft', 'ditolak'])) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }
        $user = Auth::user();
        if (!$user->hasAnyRole([
                'marketing',
                'spvmarketing',
                'admincabang'
            ])) {
                abort(403, 'Anda tidak memiliki hak untuk mengedit pengajuan.');
        }
            
        if ($user->hasRole('admincabang')) {
            $karyawan = $user->karyawan;
            if (!$karyawan) {
                abort(403, 'Data karyawan tidak ditemukan.');
            }
            if (!$karyawan->cabang_id) {
                abort(403, 'Cabang pengguna belum ditentukan.');
            }

            if ($pengajuan->cabang_id != $karyawan->cabang_id) {
                abort(403, 'Anda tidak dapat mengakses pengajuan dari cabang lain.');
            }
        }

        $marketingOptions = $this->getMarketingOptionsForUser();

        return view('pengajuans.step1',compact('pengajuan','marketingOptions'));
    }

    public function updateStep1(PengajuanStep1Request $request,Pengajuan $pengajuan) 
    {
        if (!in_array($pengajuan->status, ['draft', 'ditolak'])) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }

        $user = Auth::user();

        $karyawanUser = $user->karyawan;

        if (!$karyawanUser) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        if ($user->hasRole('admincabang')) {

            if (!$karyawanUser->cabang_id) {
                abort(403, 'Cabang pengguna belum ditentukan.');
            }

            if ($pengajuan->cabang_id != $karyawanUser->cabang_id) {
                abort(
                    403,
                    'Pengajuan bukan milik cabang Anda.'
                );
            }
        }

        $marketingId = $pengajuan->marketing_id;

        if ($user->hasRole('admincabang')) {
            $karyawanMarketing = Karyawan::query()
                ->where('id', $request->marketing_id)
                ->where('cabang_id',$karyawanUser->cabang_id)
                ->whereHas('user', function ($query) {
                    $query->role([
                        'marketing',
                        'spvmarketing'
                    ]);
                })
                ->first();

            if (!$karyawanMarketing) {
                abort(403,'Marketing yang dipilih tidak valid atau bukan berasal dari cabang Anda.');
            }

            $marketingId = $karyawanMarketing->id;
        }

        elseif (
            $user->hasAnyRole(['marketing','spvmarketing'])
        ) {
            $marketingId = $user->getMarketingId();
        }

        else {
            abort(403,'Anda tidak memiliki hak untuk mengubah pengajuan.');
        }

        DB::beginTransaction();
        try {
            $oldKategori = $pengajuan->kategori_nasabah;
            $newKategori = $request->kategori_nasabah;

            $pengajuan->update([
                'marketing_id'      => $marketingId,
                'tanggal_pengajuan' =>$request->tanggal_pengajuan,
                'nominal_pengajuan' =>$request->nominal_pengajuan,
                'tenor' =>$request->tenor,
                'kategori_nasabah' =>$newKategori,
                'status_customer' =>$request->status_customer,
                'tujuan_pinjaman' =>$request->tujuan_pinjaman,
                'catatan' =>$request->catatan,
            ]);

            if ($oldKategori != $newKategori) {

            if (
                    $oldKategori === 'payrol' && $newKategori === 'non_payrol'
                ) {
                    $invalidDocs = ['atm','bpjs','sk_kerja',];
                    $docs = Dokumen_pengajuan::where('pengajuan_id',$pengajuan->id)
                        ->whereIn('jenis_dokumen',$invalidDocs)
                        ->get();

                    foreach ($docs as $doc) {

                        Storage::disk('public')->delete($doc->file_path);
                        $doc->delete();
                    }
                }
                $pengajuan->update([
                    'documents_completed' =>false,
                    'current_step' =>4,
                ]);

                DB::commit();

                return redirect()->route('pengajuan.step4',$pengajuan->id)
                    ->with('warning','Kategori nasabah berubah. Silakan periksa ulang dokumen.');
            }


            DB::commit();

            return redirect()->route('pengajuan.step2',$pengajuan->id)
                ->with('success','Step 1 berhasil diupdate.');

        } catch (\Throwable $e) {

            DB::rollBack();
            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    public function step2(Pengajuan $pengajuan)
    {
        if (!$this->bolehEditPengajuan($pengajuan)) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }

        $pengajuan->load(['nasabah','nasabah.pekerjaanNasabah',]);

        $nasabah = $pengajuan->nasabah;

        $pekerjaan = $nasabah?->pekerjaanNasabah;

        return view('pengajuans.step2', compact('pengajuan','nasabah','pekerjaan'));
    }

    public function storeStep2(Request $request, Pengajuan $pengajuan)
    {
        // if ($pengajuan->status != 'draft') {
        //     abort(403, 'Pengajuan sudah dikirim');
        // }
        if (!$this->bolehEditPengajuan($pengajuan)) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }

        $nasabahLama = Nasabah::where('pengajuan_id',$pengajuan->id)->first();

        $pekerjaanLama = $nasabahLama?->pekerjaanNasabah;

        $oldStatusPerkawinan = $nasabahLama?->status_perkawinan;

        $oldJenisPekerjaan = $pekerjaanLama?->jenis_pekerjaan;

        $request->merge([
            'penghasilan' => parse_rupiah($request->penghasilan),
        ]);

        $validated = $request->validate([
            'nama' => 'required',
            'nik' => 'required|min:16|max:16',
            'tempat_lahir' => 'required',
            'tgl_lahir' => 'required|date',
            'no_hp' => 'required',
            'alamat' => 'required',
            'status_perkawinan' => 'required',
            'jumlah_tanggungan' => 'required|integer',
            'status_rumah' => 'required',
            'lama_menetap_tahun' => 'required|integer',
            'lama_menetap_bulan' => 'required|integer',

            'foto_nasabah' => $nasabahLama?->foto_nasabah
                ? 'nullable|image|mimes:jpg,jpeg,png|max:5120'
                : 'required|image|mimes:jpg,jpeg,png|max:5120',
            'ktp_nasabah' => $nasabahLama?->ktp_nasabah
                ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
                : 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',

            'akte_lahir_nasabah' =>'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'jenis_pekerjaan' => 'required',
            'penghasilan' => 'required|numeric',
            'nama_usaha' => 'nullable',
            'jenis_usaha' => 'nullable',
            'lama_usaha' => 'nullable|integer',
            'jumlah_pegawai' => 'nullable|integer',
            'alamat_usaha' => 'nullable',
            'telpon_usaha' => 'nullable',
            'bangunan_usaha' => 'nullable',
            'status_tempat_usaha' => 'nullable',
            'aktivitas_usaha' => 'nullable',
        ]);

        DB::beginTransaction();

        $oldPhotoPath = $nasabahLama?->foto_nasabah;

        $oldKtpPath = $nasabahLama?->ktp_nasabah;

        $oldAktePath = $nasabahLama?->akte_lahir_nasabah;

        $newPhotoPath = $oldPhotoPath;

        $newKtpPath = $oldKtpPath;

        $newAktePath = $oldAktePath;

        try {
            if ($request->hasFile('foto_nasabah')) {
                $newPhotoPath = $request->file('foto_nasabah')->store('nasabah', 'public');
            }
            if ($request->hasFile('ktp_nasabah')) {

                $newKtpPath = $request
                    ->file('ktp_nasabah')
                    ->store('nasabah/ktp', 'public');
            }
            if ($request->hasFile('akte_lahir_nasabah')) {

                $newAktePath = $request
                    ->file('akte_lahir_nasabah')
                    ->store('nasabah/akte', 'public');
            }

            $nasabah = Nasabah::updateOrCreate(
                [
                    'pengajuan_id' => $pengajuan->id
                ],

                [
                    'nama' => $validated['nama'],
                    'nik' => $validated['nik'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tgl_lahir' => $validated['tgl_lahir'],
                    'no_hp' => $validated['no_hp'],
                    'alamat' => $validated['alamat'],
                    'status_perkawinan' =>$validated['status_perkawinan'],
                    'jumlah_tanggungan' =>$validated['jumlah_tanggungan'],
                    'status_rumah' =>$validated['status_rumah'],
                    'lama_menetap_tahun' =>$validated['lama_menetap_tahun'],
                    'lama_menetap_bulan' =>$validated['lama_menetap_bulan'],
                    'foto_nasabah' =>$newPhotoPath,
                    'ktp_nasabah' =>$newKtpPath,
                    'akte_lahir_nasabah' =>$newAktePath,
                ]
            );

            Pekerjaan_nasabah::updateOrCreate(
                [
                    'nasabah_id' => $nasabah->id
                ],
                [
                    'jenis_pekerjaan' =>$validated['jenis_pekerjaan'],
                    'penghasilan' =>$validated['penghasilan'],
                    'nama_usaha' =>$validated['nama_usaha'] ?? null,
                    'jenis_usaha' =>$validated['jenis_usaha'] ?? null,
                    'lama_usaha' =>$validated['lama_usaha'] ?? null,
                    'jumlah_pegawai' =>$validated['jumlah_pegawai'] ?? null,
                    'alamat_usaha' =>$validated['alamat_usaha'] ?? null,
                    'telpon_usaha' =>$validated['telpon_usaha'] ?? null,
                    'bangunan_usaha' =>$validated['bangunan_usaha'] ?? null,
                    'status_tempat_usaha' =>$validated['status_tempat_usaha'] ?? null,
                    'aktivitas_usaha' =>$validated['aktivitas_usaha'] ?? null,
                ]
            );


            $requireDocumentReview =$oldStatusPerkawinan !==$validated['status_perkawinan'] ||
                $oldJenisPekerjaan !== $validated['jenis_pekerjaan'];


            if ($requireDocumentReview) {
                $pengajuan->update(['documents_completed' => false,'current_step' => 3,]);

                DB::commit();

                if ( $request->hasFile('foto_nasabah') && $oldPhotoPath &&$oldPhotoPath !== $newPhotoPath
                ) {
                    Storage::disk('public')->delete(
                        $oldPhotoPath
                    );
                }

                if ( $request->hasFile('ktp_nasabah') && $oldKtpPath && $oldKtpPath !== $newKtpPath
                ) {
                    Storage::disk('public')->delete($oldKtpPath);
                }

                if ($request->hasFile('akte_lahir_nasabah')&&$oldAktePath&&$oldAktePath !== $newAktePath
                ) {
                    Storage::disk('public')->delete($oldAktePath);
                }

                return redirect()->route('pengajuan.step3',$pengajuan->id)
                    ->with('warning','Data nasabah atau pekerjaan berubah. Silakan periksa kembali Step 3 dan dokumen Step 4.');
            }

            $pengajuan->update(['current_step' => max($pengajuan->current_step,3),]);
            DB::commit();
            if ($request->hasFile('foto_nasabah')&&$oldPhotoPath&&$oldPhotoPath !== $newPhotoPath)
                {
                    Storage::disk('public')->delete($oldPhotoPath);
                }

            if ($request->hasFile('ktp_nasabah')&&$oldKtpPath&&$oldKtpPath !== $newKtpPath) 
                {
                    Storage::disk('public')->delete($oldKtpPath);
                }


            if ($request->hasFile('akte_lahir_nasabah')&&$oldAktePath&&$oldAktePath !== $newAktePath) 
                {
                Storage::disk('public')->delete($oldAktePath);
            }

            return redirect()->route('pengajuan.step3',$pengajuan->id)
                ->with('success','Data nasabah dan pekerjaan berhasil disimpan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->hasFile('foto_nasabah')&&$newPhotoPath&&$newPhotoPath !== $oldPhotoPath) {
                Storage::disk('public')->delete(
                    $newPhotoPath
                );
            }

            if ($request->hasFile('ktp_nasabah')&&$newKtpPath&&$newKtpPath !== $oldKtpPath) 
                {
                Storage::disk('public')->delete($newKtpPath);
            }

            if ($request->hasFile('akte_lahir_nasabah')&&$newAktePath&&$newAktePath !== $oldAktePath) 
                {
                Storage::disk('public')->delete($newAktePath);
            }

            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    public function step3(Pengajuan $pengajuan)
    {
         if (!$this->bolehEditPengajuan($pengajuan)) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }
    
        $pengajuan->load(['referensis','referensis.pekerjaan']);
        $pasangan = $pengajuan->referensis->where('jenis','pasangan')->first();
        $penjamin = $pengajuan->referensis->where('jenis','penjamin')->first();
        // $saudaras = $pengajuan->referensis->where('jenis','saudara')->sortBy('urutan');
        $saudaras = $pengajuan->referensis->where('jenis','saudara')->sortBy('urutan')->values();
        return view('pengajuans.step3',compact('pengajuan','pasangan','penjamin','saudaras'));
    }

    public function storeStep3(PengajuanStep3Request $request, Pengajuan $pengajuan) 
    {
        DB::beginTransaction();

        /*
        |--------------------------------------------------------------------------
        | FILE TRACKING
        |--------------------------------------------------------------------------
        */

        $newPhotoPath = null;
        $oldPhotoPath = null;

        try {

            /*
            |--------------------------------------------------------------------------
            | PASANGAN
            |--------------------------------------------------------------------------
            */

            if ($request->boolean('has_pasangan')) {

                /*
                |--------------------------------------------------------------------------
                | CARI PASANGAN LAMA
                |--------------------------------------------------------------------------
                */

                $oldPasangan = Referensi::where('pengajuan_id', $pengajuan->id)
                    ->where('jenis', 'pasangan')
                    ->first();

                $oldPhotoPath = $oldPasangan?->foto_pasangan;


                /*
                |--------------------------------------------------------------------------
                | FOTO PASANGAN
                |--------------------------------------------------------------------------
                */

                $photoPath = $oldPhotoPath;

                /*
                | Jika upload foto baru
                */

                if ($request->hasFile('pasangan.foto_pasangan')) {

                    $newPhotoPath = $request
                        ->file('pasangan.foto_pasangan')
                        ->store('pasangan', 'public');

                    $photoPath = $newPhotoPath;
                }


                /*
                |--------------------------------------------------------------------------
                | PASANGAN BARU WAJIB FOTO
                |--------------------------------------------------------------------------
                */

                if (!$oldPasangan && !$photoPath) {

                    throw ValidationException::withMessages([
                        'pasangan.foto_pasangan' =>
                            'Foto pasangan wajib diupload.'
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | SIMPAN DATA PASANGAN
                |--------------------------------------------------------------------------
                */

                $pasangan = Referensi::updateOrCreate(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'jenis' => 'pasangan',
                    ],
                    [
                        'nama'          => $request->pasangan['nama'] ?? null,
                        'tempat_lahir'  => $request->pasangan['tempat_lahir'] ?? null,
                        'tgl_lahir'     => $request->pasangan['tgl_lahir'] ?? null,
                        'no_hp'         => $request->pasangan['no_hp'] ?? null,
                        'foto_pasangan' => $photoPath,
                    ]
                );


                /*
                |--------------------------------------------------------------------------
                | PEKERJAAN PASANGAN
                |--------------------------------------------------------------------------
                */

                if ($request->boolean('has_pekerjaan_pasangan')) {

                    Pekerjaan_referensi::updateOrCreate(
                        [
                            'referensi_id' => $pasangan->id
                        ],
                        [
                            'penghasilan' =>
                                $request->pasangan['pekerjaan']['penghasilan'] ?? null,

                            'nama_usaha' =>
                                $request->pasangan['pekerjaan']['nama_usaha'] ?? null,

                            'jenis_usaha' =>
                                $request->pasangan['pekerjaan']['jenis_usaha'] ?? null,

                            'lama_usaha' =>
                                $request->pasangan['pekerjaan']['lama_usaha'] ?? null,

                            'jumlah_pegawai' =>
                                $request->pasangan['pekerjaan']['jumlah_pegawai'] ?? null,

                            'alamat_usaha' =>
                                $request->pasangan['pekerjaan']['alamat_usaha'] ?? null,
                        ]
                    );

                } else {

                    Pekerjaan_referensi::where(
                        'referensi_id',
                        $pasangan->id
                    )->delete();
                }


            } else {

                /*
                |--------------------------------------------------------------------------
                | PASANGAN TIDAK ADA
                |--------------------------------------------------------------------------
                */

                $oldPasangan = Referensi::where(
                    'pengajuan_id',
                    $pengajuan->id
                )
                ->where('jenis', 'pasangan')
                ->first();

                if ($oldPasangan) {

                    /*
                    | Simpan path foto sebelum record dihapus
                    */

                    $oldPhotoPath = $oldPasangan->foto_pasangan;


                    /*
                    | Hapus pekerjaan pasangan
                    */

                    Pekerjaan_referensi::where(
                        'referensi_id',
                        $oldPasangan->id
                    )->delete();


                    /*
                    | Hapus record pasangan
                    */

                    $oldPasangan->delete();


                    /*
                    | Hapus file foto pasangan
                    */

                    if ($oldPhotoPath) {

                        Storage::disk('public')->delete(
                            $oldPhotoPath
                        );
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | PENJAMIN
            |--------------------------------------------------------------------------
            */

            if ($request->boolean('has_penjamin')) {

                $penjamin = Referensi::updateOrCreate(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'jenis' => 'penjamin'
                    ],
                    [
                        'nama' =>$request->penjamin['nama'] ?? null,
                        'tempat_lahir' =>$request->penjamin['tempat_lahir'] ?? null,
                        'tgl_lahir' =>$request->penjamin['tgl_lahir'] ?? null,
                        'hubungan' =>$request->penjamin['hubungan'] ?? null,
                        'no_hp' =>$request->penjamin['no_hp'] ?? null,
                        'alamat' =>$request->penjamin['alamat'] ?? null,
                    ]
                );


                /*
                |--------------------------------------------------------------------------
                | PEKERJAAN PENJAMIN
                |--------------------------------------------------------------------------
                */

                if ($request->boolean('has_pekerjaan_penjamin')) {

                    Pekerjaan_referensi::updateOrCreate(
                        [
                            'referensi_id' => $penjamin->id
                        ],
                        [
                            'penghasilan' =>$request->penjamin['pekerjaan']['penghasilan'] ?? null,
                            'nama_usaha' =>$request->penjamin['pekerjaan']['nama_usaha'] ?? null,
                            'jenis_usaha' =>$request->penjamin['pekerjaan']['jenis_usaha'] ?? null,
                            'lama_usaha' =>$request->penjamin['pekerjaan']['lama_usaha'] ?? null,
                            'jumlah_pegawai' =>$request->penjamin['pekerjaan']['jumlah_pegawai'] ?? null,
                            'alamat_usaha' =>$request->penjamin['pekerjaan']['alamat_usaha'] ?? null,
                        ]
                    );

                } else {

                    Pekerjaan_referensi::where('referensi_id',$penjamin->id)->delete();
                }


            } else {

                /*
                |--------------------------------------------------------------------------
                | HAPUS PENJAMIN
                |--------------------------------------------------------------------------
                */

                $oldPenjamin = Referensi::where('pengajuan_id',$pengajuan->id)
                ->where('jenis', 'penjamin')
                ->first();

                if ($oldPenjamin) {
                    Pekerjaan_referensi::where('referensi_id',$oldPenjamin->id)->delete();
                    $oldPenjamin->delete();
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SAUDARA
            | edit / tambah / hapus
            |--------------------------------------------------------------------------
            */

            $submittedIds = [];


            if (!empty($request->saudara)) {

                foreach ($request->saudara as $index => $item) {

                    /*
                    |--------------------------------------------------------------------------
                    | SKIP JIKA NAMA KOSONG
                    |--------------------------------------------------------------------------
                    */

                    if (empty($item['nama'])) {
                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DATA LAMA → UPDATE
                    |--------------------------------------------------------------------------
                    */

                    if (!empty($item['id'])) {

                        $saudara = Referensi::where(
                            'id',
                            $item['id']
                        )
                        ->where(
                            'pengajuan_id',
                            $pengajuan->id
                        )
                        ->where(
                            'jenis',
                            'saudara'
                        )
                        ->first();


                        if ($saudara) {

                            $saudara->update([
                                'urutan' =>
                                    $index + 1,

                                'nama' =>
                                    $item['nama'],

                                'tempat_lahir' =>
                                    $item['tempat_lahir'] ?? null,

                                'tgl_lahir' =>
                                    $item['tgl_lahir'] ?? null,

                                'hubungan' =>
                                    $item['hubungan'] ?? null,

                                'no_hp' =>
                                    $item['no_hp'] ?? null,

                                'alamat' =>
                                    $item['alamat'] ?? null,
                            ]);


                            $submittedIds[] = $saudara->id;
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DATA BARU → CREATE
                    |--------------------------------------------------------------------------
                    */

                    else {

                        $saudara = Referensi::create([
                            'pengajuan_id' =>
                                $pengajuan->id,

                            'jenis' =>
                                'saudara',

                            'urutan' =>
                                $index + 1,

                            'nama' =>
                                $item['nama'],

                            'tempat_lahir' =>
                                $item['tempat_lahir'] ?? null,

                            'tgl_lahir' =>
                                $item['tgl_lahir'] ?? null,

                            'hubungan' =>
                                $item['hubungan'] ?? null,

                            'no_hp' =>
                                $item['no_hp'] ?? null,

                            'alamat' =>
                                $item['alamat'] ?? null,
                        ]);


                        $submittedIds[] = $saudara->id;
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | HAPUS SAUDARA YANG DIHAPUS USER
            |--------------------------------------------------------------------------
            */

            $querySaudara = Referensi::where(
                'pengajuan_id',
                $pengajuan->id
            )
            ->where(
                'jenis',
                'saudara'
            );


            /*
            | Jika tidak ada saudara yang dikirim,
            | hapus semua saudara lama.
            */

            if (empty($submittedIds)) {

                $querySaudara->delete();

            } else {

                $querySaudara
                    ->whereNotIn('id', $submittedIds)
                    ->delete();
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE WIZARD
            |--------------------------------------------------------------------------
            */

            $pengajuan->update([
                'current_step' => 4
            ]);


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | HAPUS FOTO LAMA SETELAH DATABASE COMMIT
            |--------------------------------------------------------------------------
            */

            if (
                $newPhotoPath &&
                $oldPhotoPath &&
                $oldPhotoPath !== $newPhotoPath
            ) {

                Storage::disk('public')->delete(
                    $oldPhotoPath
                );
            }


            /*
            |--------------------------------------------------------------------------
            | REDIRECT
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'pengajuan.step4',
                    $pengajuan->id
                )
                ->with(
                    'success',
                    'Step 3 berhasil disimpan.'
                );


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ROLLBACK DATABASE
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            /*
            |--------------------------------------------------------------------------
            | HAPUS FOTO BARU JIKA TRANSAKSI GAGAL
            |--------------------------------------------------------------------------
            */

            if ($newPhotoPath) {

                Storage::disk('public')->delete(
                    $newPhotoPath
                );
            }


            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function step4(Pengajuan $pengajuan,DocumentService $documentService) {
        if (!$this->bolehEditPengajuan($pengajuan)) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }
    
        $documents = $documentService->getDocuments($pengajuan);
    
        $uploaded = $pengajuan->dokumenPengajuans()->get()->keyBy('jenis_dokumen');
    
        return view('pengajuans.step4',[
            'pengajuan'=>$pengajuan,
            'documents'=>$documents,
            'uploaded'=>$uploaded,
        ]);
    }

    public function storeStep4(Request $request,Pengajuan $pengajuan,DocumentService $documentService) 
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI FILE
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'documents' => 'nullable|array',

            'documents.*' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:51200',
            ],
        ]);


        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | DAFTAR DOKUMEN
            |--------------------------------------------------------------------------
            */

            $documents = $documentService->getDocuments(
                $pengajuan
            );


            /*
            |--------------------------------------------------------------------------
            | DOKUMEN YANG SUDAH ADA
            |--------------------------------------------------------------------------
            */

            $existingDocs = $pengajuan
                ->dokumenPengajuans()
                ->get()
                ->keyBy('jenis_dokumen');


            /*
            |--------------------------------------------------------------------------
            | CEK DOKUMEN WAJIB
            |--------------------------------------------------------------------------
            */

            $missing = [];


            foreach ($documents['required'] as $doc) {

                $exists = $existingDocs->has(
                    $doc['code']
                );


                if (
                    !$request->hasFile(
                        "documents.{$doc['code']}"
                    )
                    && !$exists
                ) {

                    $missing[] = $doc['label'];
                }
            }


            /*
            |--------------------------------------------------------------------------
            | JIKA MASIH ADA YANG KURANG
            |--------------------------------------------------------------------------
            */

            if (!empty($missing)) {

                DB::rollBack();

                return back()
                    ->withErrors([
                        'error' =>
                            "Dokumen berikut masih belum lengkap:\n• "
                            . implode(
                                "\n• ",
                                $missing
                            ),
                    ])
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | UPLOAD FILE
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('documents')) {

                foreach (
                    $request->file('documents')
                    as $jenis => $file
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | DOKUMEN LAMA
                    |--------------------------------------------------------------------------
                    */

                    $existing = $existingDocs->get(
                        $jenis
                    );


                    if ($existing) {

                        Storage::disk('public')->delete(
                            $existing->file_path
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | NAMA FILE
                    |--------------------------------------------------------------------------
                    */

                    $extension =
                        $file->getClientOriginalExtension();


                    $fileName =
                        $jenis . '_' .
                        $pengajuan->id . '_' .
                        now()->format('YmdHis') .
                        '.' .
                        $extension;


                    /*
                    |--------------------------------------------------------------------------
                    | FOLDER
                    |--------------------------------------------------------------------------
                    */

                    $folder =
                        "pengajuan/{$pengajuan->id}/dokumen";


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN FILE
                    |--------------------------------------------------------------------------
                    */

                    $path = $file->storeAs(
                        $folder,
                        $fileName,
                        'public'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN DATABASE
                    |--------------------------------------------------------------------------
                    */

                    Dokumen_pengajuan::updateOrCreate(

                        [
                            'pengajuan_id' =>
                                $pengajuan->id,

                            'jenis_dokumen' =>
                                $jenis,
                        ],

                        [
                            'nama_file' =>
                                $file->getClientOriginalName(),

                            'file_path' =>
                                $path,

                            'file_size' =>
                                $file->getSize(),

                            'status' =>
                                'pending',

                            'catatan' =>
                                null,

                            'uploaded_by' =>
                                auth()->id(),
                        ]
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE STEP
            |--------------------------------------------------------------------------
            */

            $pengajuan->update([
                'documents_completed' => true,

                'current_step' => max(
                    $pengajuan->current_step,
                    5
                ),
            ]);


            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | REDIRECT REVIEW
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'pengajuan.reviewData',
                    $pengajuan
                )
                ->with(
                    'success',
                    'Dokumen pengajuan berhasil disimpan.'
                );


        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->with(
                    'error',
                    $e->getMessage()
                )
                ->withInput();
        }
    }

    public function reviewData(Pengajuan $pengajuan)
    {
        /*
        jangan boleh masuk review
        jika dokumen belum selesai
        */
    
        if (!$pengajuan->documents_completed) {
    
            return redirect()
                ->route('pengajuan.step4',$pengajuan->id)
                ->with('warning','Silakan lengkapi dokumen terlebih dahulu.');
        }
    
        // $pengajuan->load(['nasabah','nasabah.pekerjaan','referensis','referensis.pekerjaan','dokumenPengajuans','marketing','cabang']);
        $pengajuan->load([
            'nasabah',
            'nasabah.pekerjaanNasabah',
            'referensis',
            'referensis.pekerjaan',
            'dokumenPengajuans',
            'marketing',
            'cabang'
        ]);

        $pasangan = $pengajuan->referensis->where('jenis','pasangan')->first();
        $penjamin = $pengajuan->referensis->where('jenis','penjamin')->first();
        $saudaras = $pengajuan->referensis->where('jenis','saudara');
        
        return view('pengajuans.reviewdata',compact('pengajuan','pasangan','penjamin','saudaras'));
    }

    public function analisa(Pengajuan $pengajuan)
    {
        $pengajuan->load(['analisa','nasabah']);
        $analisa = $pengajuan->analisa;
        return view('pengajuans.analisa',compact('pengajuan','analisa'));
    }


    public function storeAnalisa(Request $request,Pengajuan $pengajuan)
    {
        $validated = $request->validate([
            'harga_kredit' => 'nullable',
            'kewajiban_angsuran' => 'nullable',
            'status_pemohon' => 'nullable',
            'status_tempat_tinggal' => 'nullable',
            'data_pemohon_lengkap' => 'nullable',
            'ktp_pemohon_valid' => 'nullable',
            'ktp_pasangan_valid' => 'nullable',
            'kk_valid' => 'nullable',
            'perbaikan_plafon' => 'nullable'
        ]);
    
        $ktpPasanganValid = null;

        if (strtolower($pengajuan->nasabah->status_pernikahan ?? '') === 'menikah') {
            $ktpPasanganValid = $request->ktp_pasangan_valid;
        }
        AnalisaPengajuan::updateOrCreate(
            [
                'pengajuan_id' => $pengajuan->id
            ],
            [
                'harga_kredit'          => $request->harga_kredit,
                'kewajiban_angsuran'    => $request->kewajiban_angsuran,
                'status_pemohon'        => $request->status_pemohon,
                'status_tempat_tinggal' => $request->status_tempat_tinggal,
                'data_pemohon_lengkap'  => $request->data_pemohon_lengkap,
                'ktp_pemohon_valid'     => $request->ktp_pemohon_valid,
                'ktp_pasangan_valid'    => $ktpPasanganValid,
                'kk_valid'              => $request->kk_valid,
                'perbaikan_plafon'      => $request->perbaikan_plafon,
                'created_by'            => auth()->id()
            ]
        );
    
        return redirect()->route('pengajuan.jaminan',$pengajuan->id);
    }


    public function jaminan(Pengajuan $pengajuan)
    {
        if (!$this->bolehEditPengajuan($pengajuan)) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }
        $pengajuan->load(['jaminanPengajuans.dokumenJaminans',]);

        $jaminans = $pengajuan->jaminanPengajuans;

        return view('pengajuans.jaminan',compact('pengajuan','jaminans'));
    }

    public function storeJaminan(Request $request,Pengajuan $pengajuan) 
    {
        // dd($request->all());
        /*
        |--------------------------------------------------------------------------
        | CEK STATUS
        |--------------------------------------------------------------------------
        */

         if (!$this->bolehEditPengajuan($pengajuan)) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | JAMINAN UTAMA
            |--------------------------------------------------------------------------
            */

            'jaminan' => ['required','array','min:1',],
            'jaminan.*.id' => ['nullable','integer',],
            'jaminan.*.jenis_jaminan' => ['required','in:BPKB Motor,BPKB Mobil,Surat Tanah,SK Kerja',],


            /*
            |--------------------------------------------------------------------------
            | DATA KENDARAAN
            |--------------------------------------------------------------------------
            */

            'jaminan.*.jenis_kendaraan' => ['nullable','in:motor,mobil',],
            'jaminan.*.tahun_kendaraan' => ['nullable','integer','min:1900','max:' . (date('Y') + 1),],
            'jaminan.*.merk_kendaraan' => ['nullable','string','max:200',],
            'jaminan.*.plat_polisi' => ['nullable','string','max:200',],
            'jaminan.*.bpkb_status' => ['nullable','in:ada,tidak_ada',],
            'jaminan.*.pajak_stnk_status' => ['nullable','in:ada,tidak_ada',],
            'jaminan.*.status_pajak' => ['nullable','in:hidup,mati',],
            'jaminan.*.bpkb_atas_nama' => ['nullable','string','max:200',],
            'jaminan.*.no_bpkb' => ['nullable','string','max:200',],
            'jaminan.*.no_rangka' => ['nullable','string','max:200',],
            'jaminan.*.no_mesin' => ['nullable','string','max:200',],

            /*
            |--------------------------------------------------------------------------
            | DATA TANAH
            |--------------------------------------------------------------------------
            */

            'jaminan.*.skt_spgr_status' => ['nullable','in:ada,tidak_ada',],
            'jaminan.*.skt_spgr_dikeluarkan_oleh' => ['nullable','in:camat,kepala_desa,kepala_dusun,bawah_tangan',],
            'jaminan.*.sertifikat_status' => ['nullable','in:ada,tidak_ada',],

            /*
            |--------------------------------------------------------------------------
            | DATA UMUM JAMINAN
            |--------------------------------------------------------------------------
            */
            'jaminan.*.nama_jaminan' => ['nullable','string','max:255',],
            'jaminan.*.nilai_taksiran' => ['nullable',],
            'jaminan.*.detail_jaminan' => ['nullable','string',],

             /*
            |--------------------------------------------------------------------------
            | SK KERJA
            |--------------------------------------------------------------------------
            */

            'jaminan.*.no_sk_kerja' => ['nullable','string','max:255',],
            
            /*
            |--------------------------------------------------------------------------
            | DOKUMEN JAMINAN
            |--------------------------------------------------------------------------
            */
            'jaminan.*.files' => ['nullable','array',],
            'jaminan.*.files.*' => ['file','mimes:jpg,jpeg,png,pdf','max:51200',],

            /*
            |--------------------------------------------------------------------------
            | DOKUMEN PAYROLL
            |--------------------------------------------------------------------------
            */
            'dokumen_payroll' => ['nullable','array',],
            'dokumen_payroll.bpjs_ketenagakerjaan' => ['nullable','array',],
            'dokumen_payroll.bpjs_ketenagakerjaan.*' => ['file','mimes:jpg,jpeg,png,pdf','max:51200',],
            'dokumen_payroll.buku_tabungan' => ['nullable','array',],
            'dokumen_payroll.buku_tabungan.*' => ['file','mimes:jpg,jpeg,png,pdf','max:51200',],
            'dokumen_payroll.atm' => ['nullable','array',],
            'dokumen_payroll.atm.*' => ['file','mimes:jpg,jpeg,png,pdf','max:51200',],
            'dokumen_payroll.slip_gaji' => ['nullable','array',],
            'dokumen_payroll.slip_gaji.*' => ['file','mimes:jpg,jpeg,png,pdf','max:51200',],

        ]);

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */
        DB::beginTransaction();
        /*
        |--------------------------------------------------------------------------
        | TRACK FILE BARU
        |--------------------------------------------------------------------------
        */
        $newFiles = [];
        try {
            /*
            |--------------------------------------------------------------------------
            | JAMINAN LAMA
            |--------------------------------------------------------------------------
            */
            $existingJaminans = $pengajuan->jaminanPengajuans()->with('dokumenJaminans')->get()->keyBy('id');
            /*
            |--------------------------------------------------------------------------
            | ID JAMINAN YANG MASIH ADA
            |--------------------------------------------------------------------------
            */

            $submittedIds = collect($validated['jaminan'])
                ->pluck('id')->filter()
                ->map(
                    fn ($id) => (int) $id
                )->values()->toArray();


            /*
            |--------------------------------------------------------------------------
            | HAPUS JAMINAN YANG DIHAPUS DARI FORM
            |--------------------------------------------------------------------------
            */

            foreach (
                $existingJaminans as $existingJaminan
            ) {

                if (
                    !in_array($existingJaminan->id,$submittedIds)
                ) {

                    /*
                    | Hapus file fisik
                    */
                    foreach ($existingJaminan->dokumenJaminans as $dokumen) {
                        if ($dokumen->file_path) {
                            Storage::disk('public')->delete($dokumen->file_path);
                        }
                    }

                    /*
                    | Hapus database
                    */
                    $existingJaminan->dokumenJaminans()->delete();
                    $existingJaminan->delete();
                }
            }


            /*
            |--------------------------------------------------------------------------
            | PROSES JAMINAN
            |--------------------------------------------------------------------------
            */
            foreach ($validated['jaminan']as $data) {
                /*
                |--------------------------------------------------------------------------
                | JAMINAN LAMA / BARU
                |--------------------------------------------------------------------------
                */
                $jaminan = null;
                if (!empty($data['id'])) {
                    $jaminan =$existingJaminans->get((int) $data['id']);
                    /*
                    | Security:
                    | ID harus milik pengajuan ini.
                    */
                    if (!$jaminan) {abort(403,'Data jaminan tidak valid.');}
                }

                /*
                |--------------------------------------------------------------------------
                | JENIS JAMINAN
                |--------------------------------------------------------------------------
                */
                $jenisJaminan =$data['jenis_jaminan'];
                $isKendaraan =in_array($jenisJaminan,['BPKB Motor','BPKB Mobil',]);
                $isTanah =$jenisJaminan ==='Surat Tanah';
                $isSKKerja = $jenisJaminan === 'SK Kerja';

        /*
        |--------------------------------------------------------------------------
        | VALIDASI KHUSUS SK KERJA
        |--------------------------------------------------------------------------
        */

        if ($isSKKerja) {

            if (empty($data['no_sk_kerja'])) {

                throw new \Exception(
                    'Nomor SK Kerja wajib diisi.'
                );

            }

            if (empty($data['files'])) {

                throw new \Exception(
                    'Dokumen SK Kerja wajib diupload.'
                );

            }
        }
                /*
                |--------------------------------------------------------------------------
                | DATA JAMINAN
                |--------------------------------------------------------------------------
                */
                $jaminanData = [
                    'pengajuan_id' =>$pengajuan->id,
                    'jenis_jaminan' =>$jenisJaminan,

                    /*
                    |--------------------------------------------------------------------------
                    | KENDARAAN
                    |--------------------------------------------------------------------------
                    */
                    'jenis_kendaraan' =>$isKendaraan? ($data['jenis_kendaraan'] ?? null): null,
                    'tahun_kendaraan' =>$isKendaraan? ($data['tahun_kendaraan'] ?? null): null,
                    'merk_kendaraan' =>$isKendaraan? ($data['merk_kendaraan'] ?? null): null,
                    'plat_polisi' =>$isKendaraan? ($data['plat_polisi'] ?? null): null,
                    'bpkb_status' =>$isKendaraan? ($data['bpkb_status'] ?? null): null,
                    'pajak_stnk_status' =>$isKendaraan? ($data['pajak_stnk_status'] ?? null): null,
                    'status_pajak' =>$isKendaraan? ($data['status_pajak'] ?? null): null,
                    'bpkb_atas_nama' =>$isKendaraan? ($data['bpkb_atas_nama'] ?? null): null,
                    'no_bpkb' =>$isKendaraan? ($data['no_bpkb'] ?? null): null,
                    'no_rangka' =>$isKendaraan? ($data['no_rangka'] ?? null): null,
                    'no_mesin' =>$isKendaraan? ($data['no_mesin'] ?? null): null,

                    /*
                    |--------------------------------------------------------------------------
                    | SURAT TANAH
                    |--------------------------------------------------------------------------
                    */

                    'skt_spgr_status' =>$isTanah? ($data['skt_spgr_status'] ?? null): null,
                    'skt_spgr_dikeluarkan_oleh' =>$isTanah? ($data['skt_spgr_dikeluarkan_oleh'] ?? null): null,
                    'sertifikat_status' =>$isTanah? ($data['sertifikat_status'] ?? null): null,

                          
                    // SK KERJA

                    'no_sk_kerja' =>$isSKKerja? ($data['no_sk_kerja'] ?? null): null,

                    /*
                    |--------------------------------------------------------------------------
                    | UMUM
                    |--------------------------------------------------------------------------
                    */

                    'nama_jaminan' => $isSKKerja ? 'SK Kerja' : ($data['nama_jaminan'] ?? null),

                    'nilai_taksiran' =>
                        parse_rupiah(
                            $data['nilai_taksiran'] ?? null
                        ),

                    'detail_jaminan' =>
                        $data['detail_jaminan'] ?? null,
                ];

                if ($jaminan) {

                    /*
                    |--------------------------------------------------------------------------
                    | Jika jenis jaminan berubah
                    |--------------------------------------------------------------------------
                    */
                    if ($jaminan->jenis_jaminan !== $jenisJaminan) {
                        foreach ($jaminan->dokumenJaminans as $dokumen) {
                            if ($dokumen->file_path) {
                                Storage::disk('public')->delete($dokumen->file_path);
                            }
                        }
                        $jaminan->dokumenJaminans()->delete();
                    }
                    $jaminan->update($jaminanData);
                } else {
                    $jaminan = JaminanPengajuan::create($jaminanData);
                }

                /*
                |--------------------------------------------------------------------------
                | UPDATE / CREATE
                |--------------------------------------------------------------------------
                */

                if ($jaminan) {

                    $jaminan->update(
                        $jaminanData
                    );

                } else {

                    $jaminan =
                        JaminanPengajuan::create(
                            $jaminanData
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | DOKUMEN JAMINAN
                |--------------------------------------------------------------------------
                */

                if (
                    !empty($data['files'])
                ) {

                    foreach (
                        $data['files']
                        as $file
                    ) {

                        $originalName =
                            $file->getClientOriginalName();


                        $extension =
                            $file->getClientOriginalExtension();


                        $storedName =
                            Str::uuid()
                            . '.'
                            . $extension;


                        $folder =
                            "pengajuan/{$pengajuan->id}/jaminan/{$jaminan->id}";


                        $path =
                            $file->storeAs(
                                $folder,
                                $storedName,
                                'public'
                            );


                        $newFiles[] =
                            $path;


                        DokumenJaminan::create([

                            'jaminan_pengajuan_id' =>
                                $jaminan->id,

                            'jenis_dokumen' =>
                                'dokumen_jaminan',

                            'nama_file' =>
                                $originalName,

                            'file_path' =>
                                $path,

                            'file_size' =>
                                $file->getSize(),

                            'mime_type' =>
                                $file->getMimeType(),

                            'uploaded_by' =>
                                auth()->id(),
                        ]);
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | DOKUMEN PAYROLL
            |--------------------------------------------------------------------------
            */

            $jenisPayroll = [

                'bpjs_ketenagakerjaan' =>
                    'Kartu Jamsostek / BPJS Ketenagakerjaan',

                'buku_tabungan' =>
                    'Buku Tabungan',

                'atm' =>
                    'ATM',

                'slip_gaji' =>
                    'Slip Gaji',
            ];


            if (
                !empty(
                    $validated['dokumen_payroll']
                    ?? null
                )
            ) {

                foreach (
                    $jenisPayroll as $jenisDokumen => $label
                ) {

                    $files =
                        $validated[
                            'dokumen_payroll'
                        ][$jenisDokumen] ?? [];


                    foreach (
                        $files as $file
                    ) {

                        $originalName =
                            $file->getClientOriginalName();


                        $extension =
                            $file->getClientOriginalExtension();


                        $storedName =
                            Str::uuid()
                            . '.'
                            . $extension;


                        $folder =
                            "pengajuan/{$pengajuan->id}/payroll";


                        $path =
                            $file->storeAs(
                                $folder,
                                $storedName,
                                'public'
                            );


                        $newFiles[] =
                            $path;


                        Dokumen_payroll::create([

                            'pengajuan_id' =>
                                $pengajuan->id,

                            'jenis_dokumen' =>
                                $jenisDokumen,

                            'nama_file' =>
                                $originalName,

                            'file_path' =>
                                $path,

                            'file_size' =>
                                $file->getSize(),

                            'mime_type' =>
                                $file->getMimeType(),

                            'uploaded_by' =>
                                auth()->id(),
                        ]);
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE WIZARD
            |--------------------------------------------------------------------------
            */

            $pengajuan->update([

                'current_step' => max(
                    $pengajuan->current_step,
                    7
                ),

            ]);


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | REDIRECT
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'pengajuan.kapital',
                    $pengajuan->id
                )
                ->with(
                    'success',
                    'Data jaminan dan dokumen berhasil disimpan.'
                );


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ROLLBACK
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            /*
            |--------------------------------------------------------------------------
            | HAPUS FILE BARU
            |--------------------------------------------------------------------------
            */

            foreach (
                $newFiles as $path
            ) {

                Storage::disk('public')
                    ->delete($path);
            }


            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }    

    public function kapital(Pengajuan $pengajuan)
    {
        $pengajuan->load('kapital');
        $kapital = $pengajuan->kapital;
    
        return view('pengajuans.kapital',compact('pengajuan','kapital'));
    }

    public function storeKapital(Request $request,Pengajuan $pengajuan)
    {
        $validated = $request->validate([

            'omzet_harian'         => 'nullable|integer|min:0',
            'laba_harian'          => 'nullable|integer|min:0',
            'gaji_debitur'         => 'nullable|integer|min:0',
            'pendapatan_pasangan'  => 'nullable|integer|min:0',
        
            'biaya_rumah_tangga'   => 'nullable|integer|min:0',
            'biaya_motor'          => 'nullable|integer|min:0',
            'biaya_koperasi'       => 'nullable|integer|min:0',
            'angsuran_lain'        => 'nullable|integer|min:0',
            'biaya_kontrak_rumah'  => 'nullable|integer|min:0',
            'biaya_tempat_usaha'   => 'nullable|integer|min:0',
        
            'catatan'              => 'nullable|string'
        ]);
    
        $labaBulanan = ($request->laba_harian ?? 0) * 30;

        $totalPengeluaran =
            ($request->biaya_rumah_tangga ?? 0)
            + ($request->biaya_motor ?? 0)
            + ($request->biaya_koperasi ?? 0)
            + ($request->angsuran_lain ?? 0)
            + ($request->biaya_kontrak_rumah ?? 0)
            + ($request->biaya_tempat_usaha ?? 0);
    
       $totalPendapatan =
            $labaBulanan
            + ($request->gaji_debitur ?? 0)
            + ($request->pendapatan_pasangan ?? 0);
    
        $sisaPendapatan = $totalPendapatan - $totalPengeluaran;
    
        $data = array_merge($validated, [
            'total_pengeluaran' => $totalPengeluaran,
            'sisa_pendapatan'   => $sisaPendapatan,
            'created_by'        => optional($pengajuan->kapital)->created_by ?? auth()->id(),
            'updated_by'        => auth()->id(),
        ]);
    
        DB::transaction(function () use ($pengajuan, $data) {
    
            KapitalPengajuan::updateOrCreate(
                ['pengajuan_id' => $pengajuan->id],
                $data
            );
    
        });
    
        return redirect()->route('pengajuan.reviewFinal', $pengajuan->id)
        ->with('success','Analisa kapital berhasil disimpan. Silakan lakukan Review Final.');
    }
    
    public function reviewFinal(Pengajuan $pengajuan)
    {
       if (!$this->bolehEditPengajuan($pengajuan)) {
            abort(403, 'Pengajuan tidak dapat diedit.');
        }
    
        $pengajuan->load(['nasabah','nasabah.pekerjaanNasabah','referensis','referensis.pekerjaan','marketing','cabang','analisa',
        'jaminanPengajuans','jaminanPengajuans.dokumenJaminans','dokumenPayrolls','kapital',]);
    
        $referensis = $pengajuan->referensis;
    
        $pasangan = $referensis->firstWhere('jenis', 'pasangan');
    
        $penjamin = $referensis->firstWhere('jenis', 'penjamin');
    
        $saudaras = $referensis->where('jenis', 'saudara');
    
        return view('pengajuans.review-final', [
            'pengajuan' => $pengajuan,
            'pasangan' => $pasangan,
            'penjamin' => $penjamin,
            'saudaras' => $saudaras,
            'mode' => 'review',
        ]);
    }

    public function saveReviewFinal(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->status != 'draft') {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_marketing' => 'nullable|string|max:5000',
        ]);

        DB::transaction(function () use ($pengajuan, $validated) {
            $pengajuan->update([
                'catatan_marketing' => $validated['catatan_marketing'] ?? null,
            ]);
        });

        return back()->with('success','Draft Review Final berhasil disimpan.');
    }

    public function submitReviewFinal(Request $request, Pengajuan $pengajuan)
    {
        if (!in_array($pengajuan->status, ['draft', 'ditolak'])) {
            abort(403);
        }

        $validated = $request->validate([
            'confirm_submit'     => 'accepted',
            'catatan_marketing'  => 'nullable|string|max:5000',
        ]);

        DB::transaction(function () use ($pengajuan, $validated) {
            $statusSebelumnya = $pengajuan->status;

            $statusSesudahnya = 'menunggu_pimpinan';
        
            $pengajuan->update([
                'catatan_marketing' => $validated['catatan_marketing']?? null,
                'submitted_at'      => now(),
                'status'            => $statusSesudahnya,
            ]);

            ApprovalPengajuan::create([
                'pengajuan_id'      => $pengajuan->id,
                'user_id'           => auth()->id(),
                'role_name'         =>auth()->user()->getRoleNames()->first(),
                'aksi'              => 'submit',
                'status_sebelumnya' => $statusSebelumnya,
                'status_sesudahnya' => $statusSesudahnya,
                'catatan'           => $validated['catatan_marketing'],
            ]);
        });
        return redirect()->route('pengajuan.index')->with('success','Pengajuan berhasil diajukan ke Pimpinan');
    }

    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load(['nasabah','nasabah.pekerjaanNasabah','referensis','referensis.pekerjaan','dokumenPengajuans',
            'marketing.user','cabang','analisa','jaminanPengajuans','jaminanPengajuans.dokumenJaminans','kapital','approvals.user',]);

        $referensis = $pengajuan->referensis;

        $pasangan = $referensis->firstWhere('jenis','pasangan');
        $penjamin = $referensis->firstWhere('jenis','penjamin');
        $saudaras = $referensis->where('jenis','saudara');

        return view('pengajuans.review-final', [
            'pengajuan' => $pengajuan,
            'pasangan'  => $pasangan,
            'penjamin'  => $penjamin,
            'saudaras'  => $saudaras,
            'mode'      => 'show',
        ]);
    }

    private function getMarketingOptions()
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['marketing', 'spvmarketing'])) {
            return collect([
                $user->karyawan
            ]);
        }

        if ($user->hasRole('admincabang')) {
            $cabangId = $user->karyawan?->cabang_id;

            return Karyawan::query()
                ->where('cabang_id', $cabangId)
                ->whereHas('user', function ($query) {
                    $query->role(['marketing', 'spvmarketing']);
                })
                ->with('user')
                ->get();
        }

        return collect();
    }    

    private function getMarketingOptionsForUser()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | ADMIN CABANG
        |--------------------------------------------------------------------------
        | Hanya boleh memilih marketing/spvmarketing
        | dari cabangnya sendiri.
        */

        if ($user->hasRole('admincabang')) {

            $karyawan = $user->karyawan;

            if (!$karyawan) {
                abort(403, 'Data karyawan tidak ditemukan.');
            }

            if (!$karyawan->cabang_id) {
                abort(403, 'Cabang pengguna belum ditentukan.');
            }

            return Karyawan::query()
                ->where('cabang_id', $karyawan->cabang_id)
                ->whereHas('user', function ($query) {
                    $query->role(['marketing', 'spvmarketing']);
                })
                ->with('user')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | MARKETING / SPV MARKETING
        |--------------------------------------------------------------------------
        | Tidak perlu pilihan marketing.
        */

        return collect();
    }

    private function bolehEditPengajuan(Pengajuan $pengajuan): bool
    {
        return in_array($pengajuan->status, [
            'draft',
            'ditolak',
        ]);
    }

    public function mulaiRevisi(Pengajuan $pengajuan)
    {
        if ($pengajuan->status !== 'ditolak') {
            abort(403, 'Pengajuan ini tidak dalam status ditolak.');
        }

        $user = Auth::user();

        if (!$user->hasAnyRole(['marketing','spvmarketing','admincabang'])) 
        {
            abort(403, 'Anda tidak memiliki hak untuk melakukan revisi.');
        }

        if ($user->hasRole('admincabang')) {

            $karyawan = $user->karyawan;

            if (!$karyawan) {
                abort(403, 'Data karyawan tidak ditemukan.');
            }

            if ($pengajuan->cabang_id != $karyawan->cabang_id) {
                abort(403, 'Pengajuan bukan milik cabang Anda.');
            }
        }

        $pengajuan->update([
            'current_step' => 1,
        ]);

        return redirect()->route('pengajuan.step1', $pengajuan->id)
            ->with('info','Pengajuan dibuka untuk revisi. Silakan periksa dan perbarui data mulai dari Step 1.');
    }
}
