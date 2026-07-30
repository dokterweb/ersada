<?php

namespace App\Exports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OutstandingExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $data
    ) {
    }

    public function collection()
    {
        return $this->data->map(function ($row) {

            $pengajuan = $row->pengajuan;

            $outstanding = $row->angsurans->sum('sisa_tagihan');

            $sudahBayar = $row->angsurans->where('status','dibayar')->count();

            $sisa = $row->angsurans->where('status','!=','dibayar')->count();

            return [

                'Nomor Pembiayaan' => $row->nomor_pembiayaan,

                'Nasabah' => $pengajuan?->nasabah?->nama,

                'Cabang' => $pengajuan?->cabang?->nama_cabang,

                'Marketing' => $pengajuan?->marketing?->user?->name,

                'Plafond' => $row->plafond,

                'Outstanding' => $outstanding,

                'Sudah Dibayar' => $sudahBayar,

                'Sisa Angsuran' => $sisa,

            ];

        });
    }

    public function headings(): array
    {
        return [

            'Nomor Pembiayaan',

            'Nasabah',

            'Cabang',

            'Marketing',

            'Plafond',

            'Outstanding',

            'Sudah Dibayar',

            'Sisa Angsuran',

        ];
    }
}
