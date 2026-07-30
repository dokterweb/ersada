<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PelunasanExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $data
    ) {
    }

    public function collection()
    {
        return $this->data->map(function ($row) {

            $pengajuan = $row->pembiayaan?->pengajuan;

            return [

                'Nomor Pelunasan' => $row->nomor_pelunasan,

                'Nomor Pembiayaan' => $row->pembiayaan?->nomor_pembiayaan,

                'Nasabah' => $pengajuan?->nasabah?->nama,

                'Cabang' => $pengajuan?->cabang?->nama_cabang,

                'Marketing' => $pengajuan?->marketing?->user?->name,

                'Tanggal Pelunasan' => optional($row->tanggal_pelunasan)?->format('d-m-Y'),

                'Sisa Pokok' => $row->sisa_pokok,

                'Sisa Bunga' => $row->sisa_bunga,

                'Denda' => $row->denda,

                'Diskon' => $row->diskon,

                'Total Pelunasan' => $row->total_pelunasan,

            ];

        });
    }

    public function headings(): array
    {
        return [

            'Nomor Pelunasan',

            'Nomor Pembiayaan',

            'Nasabah',

            'Cabang',

            'Marketing',

            'Tanggal Pelunasan',

            'Sisa Pokok',

            'Sisa Bunga',

            'Denda',

            'Diskon',

            'Total Pelunasan',

        ];
    }
}