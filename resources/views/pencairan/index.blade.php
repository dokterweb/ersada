@extends('layouts.app')
@section('title', 'Data Pencairan')
@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Data Pencairan
            </h2>
          </div>
         
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Pencairan</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>No. Pencairan</th>
                                <th>No. Akad</th>
                                <th>Debitur</th>
                                <th>Cabang</th>
                                <th>Tanggal</th>
                                <th class="text-end">Dana Dicairkan</th>
                                <th>Metode</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            @forelse($pencairans as $item)
                            <tr>
                                <td>{{ $loop->iteration + (($pencairans->currentPage()-1) * $pencairans->perPage()) }}</td>
                                <td><strong>{{ $item->nomor_pencairan }}</strong></td>
                                <td>{{ $item->akad->nomor_akad }}</td>
                                <td>{{ $item->akad->pembiayaan->pengajuan->nasabah->nama }}</td>
                                <td>{{ $item->akad->pembiayaan->pengajuan->cabang->nama }}</td>
                                <td>{{ $item->tanggal_pencairan->format('d-m-Y') }}</td>
                                <td class="text-end">Rp {{ number_format($item->jumlah_dicairkan,0,',','.') }}</td>
                                <td>
                                    @if($item->metode=='tunai')
                                        <span class="badge bg-green">Tunai</span>
                                    @else
                                        <span class="badge bg-blue">Transfer</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pencairan.show',$item) }}" class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    Belum ada data pencairan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $pencairans->links() }}
                </div>
            </div>
        </div>
      </div>
    </div>
    
</div>
@endsection