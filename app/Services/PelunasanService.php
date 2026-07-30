<?php

namespace App\Services;

use App\Models\Pelunasan;
use App\Models\Pembiayaan;
use Illuminate\Support\Facades\DB;

class PelunasanService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected AuditTrailService $auditTrail) {}

    public function store(Pembiayaan $pembiayaan,array $data): Pelunasan {
        return DB::transaction(function () use ($pembiayaan,$data){
            $angsurans = $pembiayaan->angsurans()->where('status','!=','dibayar')->get();
            $sisaPokok = $angsurans->sum('pokok_angsuran');
            $sisaBunga = $angsurans->sum('bunga_angsuran');
            $denda = $angsurans->sum('denda');
            $diskon = $data['diskon'] ?? 0;
            $totalPelunasan = $sisaPokok + $sisaBunga + $denda - $diskon;
            $pelunasan = Pelunasan::create([
                'pembiayaan_id' => $pembiayaan->id,
                'nomor_pelunasan' => $this->paymentService->generateNumber('PLN-'),
                'tanggal_pelunasan' => $data['tanggal_pelunasan'],
                'sisa_pokok' => $sisaPokok,
                'sisa_bunga' => $sisaBunga,
                'denda' => $denda,
                'diskon' => $diskon,
                'total_pelunasan' => $totalPelunasan,
                'keterangan' => $data['keterangan'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $pembiayaan->update(['status' => 'lunas']);

            foreach($angsurans as $angsuran){
                $angsuran->update([
                    'status' => 'dibayar',
                    'tanggal_bayar' => $data['tanggal_pelunasan'],
                    'jumlah_bayar' => $angsuran->total_angsuran,
                    'total_terbayar' => $angsuran->total_angsuran,
                    'sisa_tagihan' => 0,
                    'sisa_pokok' => 0,
                    'denda' => 0,
                ]);
            }

            // Audit Trail
            $this->auditTrail->log(
                $pembiayaan,
                'Pelunasan',
                'Pelunasan Pembiayaan',
                'Nomor Pelunasan : '.$pelunasan->nomor_pelunasan.
                ' | Total : Rp '.number_format($pelunasan->total_pelunasan,0,',','.')
            );

            return $pelunasan;
        });
    }
}