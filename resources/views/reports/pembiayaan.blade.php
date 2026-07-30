@extends('reports.layout')
@section('report-title')
Laporan Pembiayaan
@endsection
@section('report-content')

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>No Pembiayaan</th>
                <th>Nasabah</th>
                <th>Cabang</th>
                <th>Marketing</th>
                <th>Plafond</th>
                <th>Status</th>
                <th>Outstanding</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pembiayaans as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row->nomor_pembiayaan }}</td>
                <td>{{ $row->pengajuan->nasabah->nama }}</td>
                <td>{{ $row->pengajuan->cabang->nama_cabang }}</td>
                <td>{{ $row->pengajuan->marketing->name }}</td>
                <td class="text-end">
                Rp {{ number_format($row->plafond,0,',','.') }}
                </td>
                <td>
                {!! $row->status_badge !!}
                </td>
                <td class="text-end">
                Rp {{ number_format($row->angsurans->sum('sisa_tagihan'),0,',','.') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="card-footer">
    {{ $pembiayaans->links() }}
</div>
@endsection