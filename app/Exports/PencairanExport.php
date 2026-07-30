<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PencairanExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $data
    ) {
    }

    public function collection()
    {
        return $this->data->map(function ($row) {
    
            $pembiayaan = $row->akad?->pembiayaan;
            $pengajuan  = $pembiayaan?->pengajuan;
    
            return [
    
                'Nomor Pencairan' => $row->nomor_pencairan,
    
                'Nomor Pembiayaan' => $pembiayaan?->nomor_pembiayaan,
    
                'Nasabah' => $pengajuan?->nasabah?->nama,
    
                'Cabang' => $pengajuan?->cabang?->nama_cabang,
    
                'Marketing' => $pengajuan?->marketing?->user?->name,
    
                'Tanggal Pencairan' => optional($row->tanggal_pencairan)?->format('d-m-Y'),
    
                'Jumlah Dicairkan' => $row->jumlah_dicairkan,
    
                'Metode' => strtoupper($row->metode),
    
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