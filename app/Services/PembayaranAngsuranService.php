<?php

namespace App\Services;

use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranAngsuranService
{
    /**
     * Proses pembayaran angsuran
     */
    public function bayar(Angsuran $angsuran, array $data): array
    {
        return DB::transaction(function () use ($angsuran, $data) {

            // Reload data terbaru
            $angsuran->refresh();

            if ($angsuran->status === 'dibayar') {
                throw new \Exception('Angsuran ini sudah lunas.');
            }

            $jumlahBayar = (int) $data['jumlah_bayar'];

            if ($jumlahBayar <= 0) {
                throw new \Exception('Nominal pembayaran harus lebih besar dari nol.');
            }

            if ($jumlahBayar > $angsuran->sisa_tagihan) {
                throw new \Exception(
                    'Nominal pembayaran melebihi sisa tagihan Rp ' .
                    number_format($angsuran->sisa_tagihan, 0, ',', '.')
                );
            }

            $denda = 0;
            $diskon = 0;
            $totalTransaksi = $jumlahBayar + $denda - $diskon;
            $pembayaran = PembayaranAngsuran::create([
                'angsuran_id'      => $angsuran->id,
                'nomor_pembayaran' => $this->generateNomor(),
                'tanggal_bayar'    => $data['tanggal_bayar'],
                'jumlah_bayar'     => $jumlahBayar,
                'denda'            => $denda,
                'diskon'           => $diskon,
                'total_dibayar'    => $totalTransaksi,
                'metode'           => $data['metode'],
                'keterangan'       => $data['keterangan'] ?? null,
                'created_by'       => Auth::id(),
            ]);

            // $totalTerbayar = $angsuran->total_terbayar + $jumlahBayar;
            $totalTerbayar  = $angsuran->pembayaranAngsurans()->sum('jumlah_bayar');
            // $sisaTagihan = $angsuran->total_angsuran - $totalTerbayar;
            $sisaTagihan    = max(0,$angsuran->total_angsuran - $totalTerbayar);

            if ($sisaTagihan < 0) {
                $sisaTagihan = 0;
            }

            $status = $angsuran->status;

            $tanggalBayar = null;

            if ($sisaTagihan == 0) {
                $status = 'dibayar';
                $tanggalBayar = $data['tanggal_bayar'];
            } else {
                if (now()->toDateString() >= $angsuran->tanggal_jatuh_tempo->toDateString()) {
                    $status = 'jatuh_tempo';
                } else {
                    $status = 'belum_jatuh_tempo';
                }

            }

            $angsuran->update([
                'status'          => $status,
                'tanggal_bayar'   => $tanggalBayar,
                'denda'           => $denda,
                'total_terbayar'  => $totalTerbayar,
                'sisa_tagihan'    => $sisaTagihan,
            ]);

            $angsurans = $angsuran->pembiayaan->angsurans()->orderBy('angsuran_ke')->get();

            $totalAngsuran = $angsurans->count();

            $sudahDibayar = $angsurans->where('status', 'dibayar')->count();

            $sisaAngsuran = $totalAngsuran - $sudahDibayar;

            $outstanding = $angsurans
                ->where('status', '!=', 'dibayar')
                ->sortBy('angsuran_ke')
                ->first()?->sisa_pokok ?? 0;

            $this->auditTrail->log(
                $angsuran->pembiayaan,
                'Angsuran',
                'Pembayaran Angsuran',
                'Angsuran Ke-'.$angsuran->angsuran_ke.
                ' | Total : Rp '.number_format($angsuran->jumlah_bayar,0,',','.')
            );
            
            return [
                'pembayaran' => $pembayaran,
                'row' => [
                    'status' => ucfirst(str_replace('_', ' ', $status)),
                    'badge' => match ($status) {
                        'dibayar' => 'bg-success',
                        'jatuh_tempo' => 'bg-danger',
                        default => 'bg-secondary',
                    },
                    'total_terbayar'        => $totalTerbayar,
                    'total_terbayar_format' => number_format($totalTerbayar,0,',','.'),
                    'sisa_tagihan'          => $sisaTagihan,
                    'sisa_tagihan_format'   => number_format($sisaTagihan,0,',','.'),

                ],

                'summary' => [
                    'total_angsuran'        => $totalAngsuran,
                    'sudah_dibayar'         => $sudahDibayar,
                    'sisa_angsuran'         => $sisaAngsuran,
                    'outstanding'           => $outstanding,
                    'outstanding_format'    => number_format($outstanding,0,',','.'),
                ]
            ];
        });
    }

    protected function generateNomor(): string
    {
        $tahun = date('Y');

        $last = PembayaranAngsuran::whereYear('created_at', $tahun)->lockForUpdate()->count() + 1;

        return sprintf('BYR/%s/%05d',$tahun,$last);
    }
}