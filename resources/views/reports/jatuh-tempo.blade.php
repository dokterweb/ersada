@extends('reports.layout')

@section('report-title')

Laporan Jatuh Tempo Angsuran

@endsection

@section('report-content')

<div class="table-responsive">

<table class="table table-vcenter table-striped table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>No Pembiayaan</th>
            <th>Nasabah</th>
            <th>Cabang</th>
            <th>Marketing</th>
            <th>Angsuran</th>
            <th>Jatuh Tempo</th>
            <th class="text-end">Tagihan</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($angsurans as $row)
            @php
            $hari = now()->diffInDays($row->tanggal_jatuh_tempo,false);
            @endphp
            <tr>
                <td>{{ $angsurans->firstItem()+$loop->index }}</td>
                <td>{{ $row->pembiayaan->nomor_pembiayaan }}</td>
                <td>{{ $row->pembiayaan->pengajuan->nasabah->nama }}</td>
                <td>{{ $row->pembiayaan->pengajuan->cabang->nama_cabang }}</td>
                <td>{{ $row->pembiayaan->pengajuan->marketing->user->name }}</td>
                <td>Ke {{ $row->angsuran_ke }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal_jatuh_tempo)->format('d-m-Y') }}</td>
                <td class="text-end">Rp {{ number_format($row->sisa_tagihan,0,',','.') }}</td>
                <td>
                    @if($row->status=='jatuh_tempo')
                        <span class="badge bg-danger">Jatuh Tempo</span>
                        @else
                        <span class="badge bg-warning">Belum JT</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('operasional.show',$row->pembiayaan) }}" class="btn btn-sm btn-primary">
                    Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">
                Tidak ada data.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>
<div class="card-footer">
{{ $angsurans->links() }}
</div>

@endsection