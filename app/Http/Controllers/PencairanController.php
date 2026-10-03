<?php

namespace App\Http\Controllers;

use App\Models\Akad;
use App\Models\Angsuran;
use App\Models\Pembiayaan;
use App\Models\Pencairan;
use App\Services\PelunasanService;
use App\Services\PembayaranAngsuranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    protected PelunasanService $service;

    protected PembayaranAngsuranService $pembayaranAngsuranService;

    public function __construct(
        PelunasanService $service,
        PembayaranAngsuranService $pembayaranAngsuranService
    ) {
        $this->service = $service;
        $this->pembayaranAngsuranService = $pembayaranAngsuranService;
    }

    public function index()
    {
        $pencairans = Pencairan::with([
            'akad.pembiayaan.pengajuan.nasabah',
            'creator'
        ])
        ->latest()
        ->paginate(15);

        return view('pencairan.index', compact('pencairans'));
    }

   public function create(Akad $akad)
{
    $akad->load([
        'pembiayaan',
        'pembiayaan.pengajuan.nasabah',
        'pembiayaan.pengajuan.marketing.user',
        'pembiayaan.pengajuan.cabang',
        'pembiayaan.angsurans',
        'pembiayaan.pembiayaanLama',
        'pembiayaan.pembiayaanLama.pengajuan.nasabah',
        'pembiayaan.pembiayaanLama.angsurans',
        'pembiayaan.pembiayaanLama.akad.pencairan',
        'pencairan',
    ]);

    abort_if(
        $akad->status != 'signed' ||
        $akad->pembiayaan->status != 'signed',
        403,
        'Akad belum ditandatangani.'
    );

    if ($akad->pencairan) {
        return redirect()
            ->route('pencairan.show', $akad->pencairan)
            ->with(
                'warning',
                'Pencairan sudah pernah dilakukan.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBIAYAAN
    |--------------------------------------------------------------------------
    */

    $pembiayaan = $akad->pembiayaan;

    $statusCustomer =
        $pembiayaan->pengajuan->status_customer;

    /*
    |--------------------------------------------------------------------------
    | PEMBIAYAAN LAMA
    |--------------------------------------------------------------------------
    */

    $pembiayaanLama =
        $pembiayaan->pembiayaanLama;

    /*
    |--------------------------------------------------------------------------
    | NILAI DEFAULT
    |--------------------------------------------------------------------------
    |
    | Nilai ini harus tersedia untuk:
    | - baru
    | - walk_in
    | - repeat_order
    |
    */

    $danaPembiayaanBaru =
        (int) $pembiayaan->dana_diterima;

    $totalPelunasanLama = 0;

    $danaTopUp =
        $danaPembiayaanBaru;

    /*
    |--------------------------------------------------------------------------
    | PERHITUNGAN PELUNASAN PEMBIAYAAN LAMA
    |--------------------------------------------------------------------------
    */

    $perhitunganPelunasan = null;

    if (
        $statusCustomer === 'repeat_order'
        && $pembiayaanLama
    ) {

        try {

            $perhitunganPelunasan =
                $this->service->hitungPelunasan(
                    $pembiayaanLama,
                    now()->toDateString()
                );

            /*
            |------------------------------------------------------------------
            | TOTAL PELUNASAN LAMA
            |------------------------------------------------------------------
            |
            | Untuk Repeat Order, dana yang digunakan untuk melunasi
            | pembiayaan lama adalah total sebelum diskon.
            |
            */

            $totalPelunasanLama =
                (int) (
                    $perhitunganPelunasan['total_sebelum_diskon']
                    ?? $perhitunganPelunasan['total']
                    ?? 0
                );

            /*
            |------------------------------------------------------------------
            | DANA TOP UP
            |------------------------------------------------------------------
            */

            $danaTopUp =
                $danaPembiayaanBaru
                - $totalPelunasanLama;

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'Gagal menghitung pelunasan pembiayaan lama: '
                . $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ANGSURAN PERTAMA PEMBIAYAAN BARU
    |--------------------------------------------------------------------------
    */

    $angsuranPertamaBaru = $pembiayaan
        ->angsurans
        ->where('status', '!=', 'dibayar')
        ->sortBy('angsuran_ke')
        ->first();

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'pencairan.create',
        compact(
            'akad',
            'pembiayaanLama',
            'perhitunganPelunasan',
            'angsuranPertamaBaru',
            'danaPembiayaanBaru',
            'totalPelunasanLama',
            'danaTopUp'
        )
    );
}
   
    public function store(Request $request, Akad $akad)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI AKAD
        |--------------------------------------------------------------------------
        */

        abort_if(
            $akad->status != 'signed' ||
            $akad->pembiayaan->status != 'signed',
            403,
            'Akad belum ditandatangani.'
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI REQUEST
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'tanggal_pencairan' => [
                'required',
                'date',
            ],

            'tgl_telat_bayar' => [
                'required',
                'integer',
                'min:1',
                'max:30',
            ],

            'metode' => [
                'required',
                'in:tunai,transfer',
            ],

            'bank' => [
                'nullable',
                'string',
                'max:100',
            ],

            'no_rekening' => [
                'nullable',
                'string',
                'max:100',
            ],

            'atas_nama' => [
                'nullable',
                'string',
                'max:100',
            ],

            'bukti_pencairan' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:51200',
            ],

            'foto_akad1' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:51200',
            ],

            'foto_akad2' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:51200',
            ],

            'foto_akad3' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:51200',
            ],

            'video' => [
                'nullable',
                'file',
                'mimes:mp4,mov,avi,mkv,webm',
                'max:102400',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | BAYAR ANGSURAN PERTAMA
            |--------------------------------------------------------------------------
            */

            'bayar_angsuran_pertama' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK PENCAIRAN SUDAH ADA
        |--------------------------------------------------------------------------
        */

        if ($akad->pencairan) {

            return back()
                ->with(
                    'error',
                    'Pencairan sudah pernah dibuat.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        try {

            $pencairan = DB::transaction(function () use (
                $request,
                $akad
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK PEMBIAYAAN BARU
                |--------------------------------------------------------------------------
                */

                $pembiayaan = Pembiayaan::query()
                    ->whereKey($akad->pembiayaan->id)
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | PASTIKAN BELUM DICAIRKAN
                |--------------------------------------------------------------------------
                */

                if ($pembiayaan->status !== 'signed') {

                    throw new \Exception(
                        'Pembiayaan tidak dalam status siap dicairkan.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | LOAD RELASI
                |--------------------------------------------------------------------------
                */

                $pembiayaan->loadMissing([
                    'pengajuan.nasabah',
                    'pembiayaanLama',
                    'angsurans',
                ]);


                $pengajuan =
                    $pembiayaan->pengajuan;


                /*
                |--------------------------------------------------------------------------
                | DEFAULT
                |--------------------------------------------------------------------------
                */

                $jumlahDicairkan =
                    (int) $pembiayaan->dana_diterima;

                $pembiayaanLama = null;

                $pelunasanLama = null;

                $angsuranPertamaBaru = null;

                $jumlahAngsuranPertama = 0;


                /*
                |--------------------------------------------------------------------------
                | CEK APAKAH BAYAR ANGSURAN PERTAMA
                |--------------------------------------------------------------------------
                */

                $bayarAngsuranPertama =
                    $request->boolean(
                        'bayar_angsuran_pertama'
                    );


                /*
                |--------------------------------------------------------------------------
                | REPEAT ORDER / TOP UP
                |--------------------------------------------------------------------------
                */

                if (
                    $pengajuan->status_customer === 'repeat_order'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | HARUS MEMILIKI PEMBIAYAAN LAMA
                    |--------------------------------------------------------------------------
                    */

                    if (!$pembiayaan->pembiayaan_lama_id) {

                        throw new \Exception(
                            'Pembiayaan repeat order belum memiliki pembiayaan lama.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | LOCK PEMBIAYAAN LAMA
                    |--------------------------------------------------------------------------
                    */

                    $pembiayaanLama = Pembiayaan::query()
                        ->whereKey(
                            $pembiayaan->pembiayaan_lama_id
                        )
                        ->lockForUpdate()
                        ->first();


                    if (!$pembiayaanLama) {

                        throw new \Exception(
                            'Pembiayaan lama tidak ditemukan.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PEMBIAYAAN LAMA HARUS MASIH AKTIF
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $pembiayaanLama->status !== 'dicairkan'
                    ) {

                        throw new \Exception(
                            'Pembiayaan lama sudah tidak aktif atau sudah lunas.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | LOAD DATA PEMBIAYAAN LAMA
                    |--------------------------------------------------------------------------
                    */

                    $pembiayaanLama->loadMissing([
                        'pengajuan.nasabah',
                        'angsurans',
                        'akad.pencairan',
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI NIK
                    |--------------------------------------------------------------------------
                    */

                    $nikBaru =
                        $pengajuan
                            ->nasabah
                            ?->nik;

                    $nikLama =
                        $pembiayaanLama
                            ->pengajuan
                            ->nasabah
                            ?->nik;


                    if (
                        !$nikBaru ||
                        !$nikLama ||
                        $nikBaru !== $nikLama
                    ) {

                        throw new \Exception(
                            'Pembiayaan lama tidak memiliki NIK yang sama dengan nasabah repeat order.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG PELUNASAN PEMBIAYAAN LAMA
                    |--------------------------------------------------------------------------
                    |
                    | PelunasanService akan menentukan:
                    |
                    | - tenor pendek
                    | - tenor panjang
                    | - sisa pokok
                    | - sisa bunga
                    | - denda
                    |
                    */

                    $perhitungan =
                        $this->service
                            ->hitungPelunasan(
                                $pembiayaanLama,
                                $request->tanggal_pencairan
                            );


                    $totalPelunasan =
                        (int) $perhitungan[
                            'total_sebelum_diskon'
                        ];


                    /*
                    |--------------------------------------------------------------------------
                    | DANA PEMBIAYAAN BARU
                    |--------------------------------------------------------------------------
                    */

                    $danaPembiayaanBaru =
                        (int) $pembiayaan->dana_diterima;


                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG DANA TOP UP
                    |--------------------------------------------------------------------------
                    */

                    $jumlahDicairkan =
                        $danaPembiayaanBaru -
                        $totalPelunasan;


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI DANA
                    |--------------------------------------------------------------------------
                    */

                    if ($jumlahDicairkan < 0) {

                        throw new \Exception(
                            'Dana pembiayaan baru tidak mencukupi untuk melunasi pembiayaan lama.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | BUAT PELUNASAN PEMBIAYAAN LAMA
                    |--------------------------------------------------------------------------
                    */

                    $pelunasanLama =
                        $this->service->store(
                            $pembiayaanLama,
                            [
                                'tanggal_pelunasan' =>
                                    $request->tanggal_pencairan,

                                'diskon' =>
                                    0,

                                'alasan_diskon' =>
                                    null,

                                'keterangan' =>
                                    'Pelunasan pembiayaan lama melalui Top Up / Repeat Order pembiayaan '
                                    . $pembiayaan->nomor_pembiayaan,
                            ]
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | BAYAR PELUNASAN LAMA
                    |--------------------------------------------------------------------------
                    */

                    $this->service->bayar(
                        $pelunasanLama,
                        [
                            'tanggal_bayar' =>
                                $request->tanggal_pencairan,

                            'jumlah_bayar' =>
                                $pelunasanLama->total_pelunasan,
                        ]
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | PASTIKAN PEMBIAYAAN LAMA LUNAS
                    |--------------------------------------------------------------------------
                    */

                    $pembiayaanLama->refresh();

                    if (
                        $pembiayaanLama->status !== 'lunas'
                    ) {

                        throw new \Exception(
                            'Pembiayaan lama gagal dinyatakan lunas.'
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | ANGSURAN PERTAMA PEMBIAYAAN BARU
                |--------------------------------------------------------------------------
                |
                | Dilakukan setelah pembiayaan lama selesai
                | dilunasi.
                |
                */

                if ($bayarAngsuranPertama) {

                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL ANGSURAN KE-1
                    |--------------------------------------------------------------------------
                    */

                    $angsuranPertamaBaru =
                        Angsuran::query()
                            ->where(
                                'pembiayaan_id',
                                $pembiayaan->id
                            )
                            ->where(
                                'angsuran_ke',
                                1
                            )
                            ->lockForUpdate()
                            ->first();


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI ANGSURAN
                    |--------------------------------------------------------------------------
                    */

                    if (!$angsuranPertamaBaru) {

                        throw new \Exception(
                            'Angsuran pertama pembiayaan baru tidak ditemukan.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PASTIKAN BELUM DIBAYAR
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $angsuranPertamaBaru->status === 'dibayar'
                    ) {

                        throw new \Exception(
                            'Angsuran pertama pembiayaan baru sudah dibayar.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG SISA ANGSURAN
                    |--------------------------------------------------------------------------
                    */

                    $jumlahAngsuranPertama =
                        max(
                            0,
                            (int) $angsuranPertamaBaru->total_angsuran
                            -
                            (int) $angsuranPertamaBaru->total_terbayar
                        );


                    if ($jumlahAngsuranPertama <= 0) {

                        throw new \Exception(
                            'Nominal angsuran pertama tidak valid.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI DANA PENCAIRAN
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $jumlahDicairkan
                        <
                        $jumlahAngsuranPertama
                    ) {

                        throw new \Exception(
                            'Dana pencairan tidak mencukupi untuk membayar angsuran pertama.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | BAYAR ANGSURAN PERTAMA
                    |--------------------------------------------------------------------------
                    |
                    | Kita menggunakan service pembayaran yang sama
                    | dengan pembayaran normal.
                    |
                    | Dengan demikian otomatis:
                    |
                    | - membuat PembayaranAngsuran
                    | - update total_terbayar
                    | - update sisa_tagihan
                    | - update status
                    | - mencatat audit trail
                    |
                    */

                    $hasilPembayaran =
                        $this->pembayaranAngsuranService
                            ->bayar(
                                $angsuranPertamaBaru,
                                [
                                    'tanggal_bayar' =>
                                        $request->tanggal_pencairan,

                                    'jumlah_bayar' =>
                                        $jumlahAngsuranPertama,

                                    'metode' =>
                                        $request->metode,

                                    'keterangan' =>
                                        'Pembayaran angsuran pertama saat pencairan pembiayaan '
                                        . $pembiayaan->nomor_pembiayaan,
                                ]
                            );


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI PEMBAYARAN
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !isset(
                            $hasilPembayaran['alokasi']
                        )
                    ) {

                        throw new \Exception(
                            'Pembayaran angsuran pertama gagal diproses.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | KURANGI DANA YANG DITERIMA NASABAH
                    |--------------------------------------------------------------------------
                    */

                    $jumlahDicairkan -=
                        $jumlahAngsuranPertama;


                    /*
                    |--------------------------------------------------------------------------
                    | PASTIKAN TIDAK NEGATIF
                    |--------------------------------------------------------------------------
                    */

                    if ($jumlahDicairkan < 0) {

                        throw new \Exception(
                            'Dana pencairan menjadi negatif setelah pembayaran angsuran pertama.'
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | UPLOAD FILE
                |--------------------------------------------------------------------------
                */

                $folder =
                    "pencairan/{$akad->id}";


                $files = [

                    'bukti_pencairan' =>
                        'bukti_pencairan',

                    'foto_akad1' =>
                        'foto_akad1',

                    'foto_akad2' =>
                        'foto_akad2',

                    'foto_akad3' =>
                        'foto_akad3',

                    'video' =>
                        'video',

                ];


                $filePaths = [];


                foreach ($files as $field => $prefix) {

                    if ($request->hasFile($field)) {

                        $file =
                            $request->file($field);

                        $extension =
                            $file->getClientOriginalExtension();

                        $fileName =
                            $prefix .
                            '_' .
                            $akad->id .
                            '_' .
                            now()->format('YmdHis') .
                            '_' .
                            uniqid() .
                            '.' .
                            $extension;

                        $filePaths[$field] =
                            $file->storeAs(
                                $folder,
                                $fileName,
                                'public'
                            );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | SIMPAN PENCAIRAN
                |--------------------------------------------------------------------------
                */

                $pencairan =
                    Pencairan::create([

                        'akad_id' =>
                            $akad->id,

                        'nomor_pencairan' =>
                            $this->generateNomor(),

                        'tanggal_pencairan' =>
                            $request->tanggal_pencairan,

                        'tgl_telat_bayar' =>
                            $request->tgl_telat_bayar,


                        /*
                        |--------------------------------------------------------------------------
                        | JUMLAH DICAIRKAN
                        |--------------------------------------------------------------------------
                        |
                        | Normal:
                        | dana_diterima
                        |
                        | Repeat Order:
                        | dana_diterima
                        | - pelunasan pembiayaan lama
                        |
                        | Jika bayar angsuran pertama:
                        | hasil di atas
                        | - angsuran pertama
                        |
                        */

                        'jumlah_dicairkan' =>
                            $jumlahDicairkan,

                        'metode' =>
                            $request->metode,

                        'bank' =>
                            $request->bank,

                        'no_rekening' =>
                            $request->no_rekening,

                        'atas_nama' =>
                            $request->atas_nama,

                        'bukti_pencairan' =>
                            $filePaths[
                                'bukti_pencairan'
                            ] ?? null,

                        'foto_akad1' =>
                            $filePaths[
                                'foto_akad1'
                            ] ?? null,

                        'foto_akad2' =>
                            $filePaths[
                                'foto_akad2'
                            ] ?? null,

                        'foto_akad3' =>
                            $filePaths[
                                'foto_akad3'
                            ] ?? null,

                        'video' =>
                            $filePaths[
                                'video'
                            ] ?? null,

                        'keterangan' =>
                            $request->keterangan,

                        'created_by' =>
                            auth()->id(),

                    ]);


                /*
                |--------------------------------------------------------------------------
                | UPDATE PEMBIAYAAN BARU
                |--------------------------------------------------------------------------
                */

                $pembiayaan->update([

                    'status' =>
                        'dicairkan',

                    'tanggal_pencairan' =>
                        $request->tanggal_pencairan,

                ]);


                /*
                |--------------------------------------------------------------------------
                | RETURN
                |--------------------------------------------------------------------------
                */

                return $pencairan;
            });


            /*
            |--------------------------------------------------------------------------
            | REDIRECT
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'pencairan.show',
                    $pencairan
                )
                ->with(
                    'success',
                    'Pencairan berhasil disimpan.'
                );

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }
    /**
     * Detail pencairan
     */
    public function show(Pencairan $pencairan)
    {
        $pencairan->load(['creator','akad','akad.pembiayaan','akad.pembiayaan.pengajuan','akad.pembiayaan.pengajuan.nasabah',
            'akad.pembiayaan.pengajuan.marketing','akad.pembiayaan.pengajuan.cabang',]);

        return view('pencairan.show', compact('pencairan'));
    }

    public function pembiayaanLama(Pembiayaan $pembiayaan)
    {
        $pembiayaan->load([
            'pengajuan.nasabah',
            'angsurans',
            'akad.pencairan',
        ]);

        /*
        |--------------------------------------------------------------------------
        | PASTIKAN PEMBIAYAAN MASIH AKTIF
        |--------------------------------------------------------------------------
        */

        if ($pembiayaan->status !== 'dicairkan') {
            return response()->json([
                'success' => false,
                'message' => 'Pembiayaan yang dipilih sudah tidak aktif.'
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG PELUNASAN
        |--------------------------------------------------------------------------
        */

        try {

            $perhitungan = $this->service->hitungPelunasan(
                $pembiayaan,
                now()->toDateString()
            );

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [

                'id' =>
                    $pembiayaan->id,

                'nomor_pembiayaan' =>
                    $pembiayaan->nomor_pembiayaan,

                'plafond' =>
                    (int) $pembiayaan->plafond,

                'sisa_pokok' =>
                    (int) $perhitungan['sisa_pokok'],

                'sisa_bunga' =>
                    (int) $perhitungan['sisa_bunga'],

                'denda' =>
                    (int) $perhitungan['denda'],

                'total_pelunasan' =>
                    (int) $perhitungan['total_sebelum_diskon'],

            ]
        ]);
    }

    /**
     * Generate nomor pencairan
     */
    private function generateNomor()
    {
        $prefix = 'PCR';

        $tahun = date('Y');

        $last = Pencairan::whereYear('created_at', $tahun)->count() + 1;

        return sprintf("%s/%s/%05d",$prefix,$tahun,$last);
    }
}
