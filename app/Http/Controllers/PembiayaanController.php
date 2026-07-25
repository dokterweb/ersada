<?php

namespace App\Http\Controllers;

use App\Models\Pembiayaan;
use App\Models\Pengajuan;
use App\Services\PembiayaanService;
use Illuminate\Http\Request;
use App\Models\Angsuran;
use Illuminate\Support\Facades\DB;
use App\Services\AngsuranService;

class PembiayaanController extends Controller
{
    protected $service;

    public function __construct(PembiayaanService $service)
    {
        $this->service = $service;
    }

    
    public function index()
    {
        $pengajuans = Pengajuan::with(['nasabah','marketing','cabang','pembiayaan'])
        ->where('status', 'disetujui')->latest()->get();
    
        return view('pembiayaan.index', compact('pengajuans'));
    }

    public function create(Pengajuan $pengajuan)
    {
        if ($pengajuan->pembiayaan) {
            return redirect()
                ->route('pembiayaan.edit', $pengajuan->pembiayaan)
                ->with('warning','Pembiayaan sudah dibuat.');
        }
    
        abort_if($pengajuan->status != 'disetujui',403,'Pengajuan belum disetujui.');
    
        $nomorPembiayaan = $this->service->generateNomor();
    
        return view('pembiayaan.create',[
            'pengajuan'=>$pengajuan,
            'nomorPembiayaan'=>$nomorPembiayaan,
        ]);
    }

    public function store(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'plafond' => 'required|numeric|min:1',
            'tenor' => 'required|integer',
            'materai' => 'nullable|numeric|min:0',
            'biaya_survei' => 'nullable|numeric|min:0',
            'tanggal_jatuh_tempo_pertama' => 'required|date',
        ]);
    
        // Hitung ulang menggunakan Service
        $hasil = $this->service->hitung(
            $request->plafond,
            $request->tenor,
            $request->materai ?? 0,
            $request->biaya_survei ?? 0
        );
    
        $pembiayaan = Pembiayaan::create([
            'pengajuan_id'          => $pengajuan->id,
            'nomor_pembiayaan'      => $this->service->generateNomor(),
            'plafond'               => $request->plafond,
            'tenor'                 => $request->tenor,
            'jenis_tenor'           => $hasil['jenis_tenor'],
            'persen_bunga'          => $hasil['persen_bunga'],
            'persen_administrasi'   => $hasil['persen_administrasi'],
            'biaya_administrasi'    => $hasil['biaya_administrasi'],
            'materai'               => $hasil['materai'],
            'biaya_survei'          => $hasil['biaya_survei'],
            'dana_diterima'         => $hasil['dana_diterima'],
            'tanggal_jatuh_tempo_pertama' => $request->tanggal_jatuh_tempo_pertama,
            'status'                => 'draft',
            'created_by'            => auth()->id(),
        ]);
    
        return redirect()->route('pembiayaan.review', $pembiayaan->id)->with('success', 'Pembiayaan berhasil disimpan.');
    }

    public function edit(Pembiayaan $pembiayaan)
    {
        abort_if($pembiayaan->status != 'draft',403,'Pembiayaan sudah diproses dan tidak dapat diedit.');
    
        $pembiayaan->load(['pengajuan.nasabah','pengajuan.marketing','pengajuan.cabang',]);
    
        return view('pembiayaan.edit',[
            'pengajuan' => $pembiayaan->pengajuan,
            'pembiayaan' => $pembiayaan,
            'nomorPembiayaan' => $pembiayaan->nomor_pembiayaan,
        ]);
    }

    public function update(Request $request, Pembiayaan $pembiayaan)
    {
        abort_if($pembiayaan->status != 'draft',403,'Pembiayaan sudah diproses.');

        $request->validate([
            'materai' => 'required|numeric|min:0',
            'biaya_survei' => 'required|numeric|min:0',
            'tanggal_jatuh_tempo_pertama'=>'required|date',
        ]);

        $hasil = $this->service->hitung(
            $pembiayaan->plafond,
            $pembiayaan->tenor,
            $request->materai,
            $request->biaya_survei
        );

        $pembiayaan->update([
            'persen_bunga'=>$hasil['persen_bunga'],
            'persen_administrasi'=>$hasil['persen_administrasi'],
            'biaya_administrasi'=>$hasil['biaya_administrasi'],
            'materai'=>$request->materai,
            'biaya_survei'=>$request->biaya_survei,
            'dana_diterima'=>$hasil['dana_diterima'],
            'tanggal_jatuh_tempo_pertama'=>$request->tanggal_jatuh_tempo_pertama,

        ]);

        return redirect()->route('pembiayaan.review',$pembiayaan)
                ->with('success','Pembiayaan berhasil diperbarui.');
    }

    public function review(Pembiayaan $pembiayaan)
    {
        $pembiayaan->load(['pengajuan.nasabah','pengajuan.marketing','pengajuan.cabang',]);
        abort_if($pembiayaan->status != 'draft',403,'Pembiayaan sudah diproses.');
    
        return view('pembiayaan.review', compact('pembiayaan'));
    }

/* 
    public function submitReview(Request $request, Pembiayaan $pembiayaan)
    {
        $request->validate(['confirm' => 'accepted']);

        return redirect()->route('pembiayaan.index')
        ->with('success','Review pembiayaan selesai. Silakan lanjutkan ke Generate Jadwal Angsuran pada Sprint 4.2.');
    } */

    public function generateJadwal(Pembiayaan $pembiayaan,AngsuranService $service)
    {
        abort_if($pembiayaan->status != 'draft',403,'Jadwal sudah pernah dibuat.');
        $jadwal = $service->generate($pembiayaan);
    
        return view('pembiayaan.generate-jadwal',compact('pembiayaan','jadwal'));
    }

    public function storeJadwal(Request $request, Pembiayaan $pembiayaan, AngsuranService $service)
    {
        abort_if($pembiayaan->status != 'draft',403,'Jadwal sudah pernah dibuat.');
    
        abort_if($pembiayaan->angsurans()->exists(),403,'Jadwal angsuran sudah tersedia.');
    
        DB::transaction(function () use ($service, $pembiayaan) {
            $jadwal = $service->generate($pembiayaan);
    
            foreach ($jadwal as $item) {
                $pembiayaan->angsurans()->create([
                    'angsuran_ke' => $item['angsuran_ke'],
                    'tanggal_jatuh_tempo' => $item['tanggal_jatuh_tempo'],
                    'pokok_angsuran' => $item['pokok_angsuran'],
                    'bunga_angsuran' => $item['bunga_angsuran'],
                    'total_angsuran' => $item['total_angsuran'],
                    'sisa_pokok' => $item['sisa_pokok'],
                ]);
            }
    
            $pembiayaan->update(['status' => 'jadwal_generated']);
        });
    
        return redirect()->route('pembiayaan.index')->with('success','Jadwal angsuran berhasil dibuat.');
    
    }

    public function jadwal(Pembiayaan $pembiayaan)
    {
        abort_if($pembiayaan->status != 'jadwal_generated',403,'Jadwal belum dibuat.');
        $pembiayaan->load(['pengajuan.nasabah','pengajuan.marketing','pengajuan.cabang','angsurans']);

        $ringkasan = [
            'total_angsuran' => $pembiayaan->angsurans->count(),
            'total_pokok' => $pembiayaan->angsurans->sum('pokok_angsuran'),
            'total_bunga' => $pembiayaan->angsurans->sum('bunga_angsuran'),
            'total_pembayaran' => $pembiayaan->angsurans->sum('total_angsuran'),
        ];
        return view('pembiayaan.jadwal',compact('pembiayaan','ringkasan'));
    }
}
