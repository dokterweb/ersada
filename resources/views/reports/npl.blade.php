@extends('reports.layout')

@section('report-title')

Laporan Non Performing Loan (NPL)

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
            <th>Outstanding</th>
            <th>Hari Tunggakan</th>
            <th>Kolektibilitas</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($angsurans as $row)
        @php
        $hari=max(0,\Carbon\Carbon::parse($row->tanggal_jatuh_tempo)->diffInDays(now()));
        @endphp
        <tr>
            <td>{{ $angsurans->firstItem()+$loop->index }}</td>
            <td>{{ $row->pembiayaan->nomor_pembiayaan }}</td>
            <td>{{ $row->pembiayaan->pengajuan->nasabah->nama }}</td>
            <td>{{ $row->pembiayaan->pengajuan->cabang->nama_cabang }}</td>
            <td>{{ $row->pembiayaan->pengajuan->marketing->user->name }}</td>
            <td>Ke {{ $row->angsuran_ke }}</td>
            <td class="text-end">Rp {{ number_format($row->sisa_tagihan,0,',','.') }}</td>
            <td>{{ $hari }} Hari</td>
            <td>
                @if($hari<=30)
                    <span class="badge bg-warning">Dalam Perhatian</span>
                @elseif($hari<=60)
                    <span class="badge bg-orange">Kurang Lancar</span>
                @elseif($hari<=90)
                    <span class="badge bg-danger">Diragukan</span>
                @else
                    <span class="badge bg-dark">Macet</span>
                @endif
            </td>
            <td>
                <a href="{{ route('operasional.show',$row->pembiayaan) }}" class="btn btn-primary btn-sm">
                Detail
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="text-center">
            Tidak ada data NPL.
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