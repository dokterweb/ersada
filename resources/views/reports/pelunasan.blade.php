@extends('reports.layout')

@section('report-title')

Laporan Pelunasan

@endsection

@section('report-content')

<div class="table-responsive">
<table class="table table-vcenter table-hover table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>No Pelunasan</th>
            <th>No Pembiayaan</th>
            <th>Nasabah</th>
            <th>Cabang</th>
            <th>Marketing</th>
            <th>Tanggal</th>
            <th class="text-end">Total Pelunasan</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @forelse($pelunasans as $row)
        <tr>
            <td>{{ $pelunasans->firstItem() + $loop->index }}</td>
            <td>{{ $row->nomor_pelunasan }}</td>
            <td>{{ $row->pembiayaan->nomor_pembiayaan }}</td>
            <td>{{ $row->pembiayaan->pengajuan->nasabah->nama }}</td>
            <td>{{ $row->pembiayaan->pengajuan->cabang->nama_cabang }}</td>
            <td>{{ $row->pembiayaan->pengajuan->marketing->user->name }}</td>
            <td>{{ \Carbon\Carbon::parse($row->tanggal_pelunasan)->format('d-m-Y') }}</td>
            <td class="text-end">Rp {{ number_format($row->total_pelunasan,0,',','.') }}</td>
            <td>
                <span class="badge bg-success">Lunas</span>
            </td>
            <td>
                <a href="{{ route('pelunasan.show',$row) }}" class="btn btn-sm btn-primary">
                    Detail
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="text-center">
                Tidak ada data.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
</div>

<div class="card-footer">
    {{ $pelunasans->links() }}
</div>

@endsection