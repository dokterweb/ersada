<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class JatuhTempoExport implements FromCollection, WithHeadings
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

                'Nomor Pembiayaan' => $row->pembiayaan?->nomor_pembiayaan,

                'Nasabah' => $pengajuan?->nasabah?->nama,

                'Cabang' => $pengajuan?->cabang?->nama_cabang,

                'Marketing' => $pengajuan?->marketing?->user?->name,

                'Angsuran Ke' => $row->angsuran_ke,

                'Tanggal Jatuh Tempo' => optional($row->tanggal_jatuh_tempo)?->format('d-m-Y'),

                'Pokok' => $row->pokok_angsuran,

                'Bunga' => $row->bunga_angsuran,

                'Total Angsuran' => $row->total_angsuran,

                'Status' => strtoupper($row->status),

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

            'Angsuran Ke',

            'Tanggal Jatuh Tempo',

            'Pokok',

            'Bunga',

            'Total Angsuran',

            'Status',

        ];
    }
}