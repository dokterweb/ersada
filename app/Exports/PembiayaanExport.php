<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PembiayaanExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $data
    ) {
    }

    public function collection()
    {
        return $this->data->map(function ($row) {

            return [

                'Nomor Pembiayaan' => $row->nomor_pembiayaan,

                'Nasabah' => $row->pengajuan->nasabah->nama,

                'Cabang' => $row->pengajuan->cabang->nama_cabang,

                'Marketing' => $row->pengajuan->marketing?->user?->name,

                'Plafond' => $row->plafond,

                'Tenor' => $row->tenor,

                'Status' => strtoupper($row->status),

                'Tanggal Akad' => optional($row->tanggal_akad)
                                ? \Carbon\Carbon::parse($row->tanggal_akad)->format('d-m-Y')
                                : '',

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

            'Tenor',

            'Status',

            'Tanggal Akad',

        ];
    }
}