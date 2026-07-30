@extends('reports.layout')

@section('report-title')

Laporan Outstanding Pembiayaan

@endsection

@section('report-content')

<div class="table-responsive">
    <table class="table table-vcenter table-hover table-striped">
        <thead>
        <tr>
            <th width="50">No</th>
            <th>No Pembiayaan</th>
            <th>Nasabah</th>
            <th>Cabang</th>
            <th>Marketing</th>
            <th class="text-end">Plafond</th>
            <th class="text-end">Sudah Dibayar</th>
            <th class="text-end">Outstanding</th>
            <th width="180">Progress</th>
            <th>Status</th>
            <th width="90">Aksi</th>
        </tr>
        </thead>
        <tbody>
            {{-- {{ dd($pembiayaans->first()) }} --}}
        @forelse($pembiayaans as $row)
            @php
                $outstanding = $row->angsurans->sum('sisa_tagihan');
                $terbayar = $row->plafond - $outstanding;
                $progress = $row->plafond > 0
                    ? ($terbayar / $row->plafond) * 100
                    : 0;
            @endphp 
            {{-- {{ $row->id }}-{{ $row->nomor_pembiayaan }}-{{ $row->angsurans->count() }} --}}

            {{-- {{ $row->angsurans()->sum('sisa_tagihan') }} --}}
            <tr>
                <td>{{ $pembiayaans->firstItem() + $loop->index }}</td>
                <td><strong>{{ $row->nomor_pembiayaan }}</strong></td>
                <td>{{ $row->pengajuan->nasabah->nama }}</td>
                <td>{{ $row->pengajuan->cabang->nama_cabang }}</td>
                <td>{{ $row->pengajuan->marketing->user->name }}</td>
                <td class="text-end">Rp {{ number_format($row->plafond,0,',','.') }}</td>
                <td class="text-end text-success">Rp {{ number_format($terbayar,0,',','.') }}</td>
                <td class="text-end text-danger">Rp {{ number_format($outstanding,0,',','.') }}</td>
                <td>
                    <div class="progress progress-sm">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ round($progress) }}%;"
                            aria-valuenow="{{ round($progress) }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted">{{ round($progress) }} %</small>
                </td>
                <td>
                    @if($outstanding <= 0)
                        <span class="badge bg-success">Lunas</span>
                    @elseif($progress >= 80)
                        <span class="badge bg-primary">Hampir Lunas</span>
                    @elseif($progress >= 50)
                        <span class="badge bg-warning">Berjalan</span>
                    @else
                        <span class="badge bg-danger">Awal Pembiayaan</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('operasional.show',$row) }}" class="btn btn-primary btn-sm">
                        Detail
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center text-muted py-4">
                    Tidak ada data outstanding.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer d-flex justify-content-between align-items-center">
    <div>
        Menampilkan
        <strong>{{ $pembiayaans->firstItem() ?? 0 }}</strong>
        -
        <strong>{{ $pembiayaans->lastItem() ?? 0 }}</strong>
        dari
        <strong>{{ $pembiayaans->total() }}</strong>
        data
    </div>
    <div>
        {{ $pembiayaans->links() }}
    </div>
</div>

@endsection