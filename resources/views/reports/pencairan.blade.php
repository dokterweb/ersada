@extends('reports.layout')

@section('report-title')
Laporan Pencairan
@endsection

@section('report-content')

<div class="table-responsive">
<table class="table table-vcenter table-striped table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>No Pencairan</th>
            <th>No Pembiayaan</th>
            <th>Nasabah</th>
            <th>Cabang</th>
            <th>Marketing</th>
            <th>Tanggal</th>
            <th class="text-end">Jumlah</th>
            <th>Metode</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @forelse($pencairans as $row)
        <tr>
            <td>{{ $pencairans->firstItem() + $loop->index }}</td>
            <td>{{ $row->nomor_pencairan }}</td>
            <td>{{ $row->akad->pembiayaan->nomor_pembiayaan }}</td>
            <td>{{ $row->akad->pembiayaan->pengajuan->nasabah->nama }}</td>
            <td>{{ $row->akad->pembiayaan->pengajuan->cabang->nama_cabang }}</td>
            <td>{{ $row->akad->pembiayaan->pengajuan->marketing->user->name }}</td>
            <td>{{ $row->tanggal_pencairan->format('d-m-Y') }}</td>
            <td class="text-end">Rp {{ number_format($row->jumlah_dicairkan,0,',','.') }}</td>
            <td><span class="badge bg-blue-lt">{{ ucfirst($row->metode) }}</span></td>
            <td>
                <a href="{{ route('pencairan.show',$row) }}" class="btn btn-sm btn-primary">
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
    {{ $pencairans->links() }}
</div>

@endsection