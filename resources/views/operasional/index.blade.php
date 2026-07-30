@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Data Karyawan</h2>
            </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-wallet"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ $totalAktif }}
                            </div>
                            <div class="text-muted">
                            Total Aktif
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                        <span class="bg-green text-white avatar">
                            <i class="fas fa-money-bill-wave"></i>
                        </span>
                        </div>
                        <div class="col">
                        <div class="font-weight-medium">
                            Rp {{ number_format($totalOutstanding,0,',','.') }}
                        </div>
                        <div class="text-muted">
                            Outstanding
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                        <span class="bg-twitter text-white avatar">
                            <i class="fas fa-calendar"></i>
                        </span>
                        </div>
                        <div class="col">
                        <div class="font-weight-medium">
                            {{ $jatuhTempoHariIni }}
                        </div>
                        <div class="text-muted">
                            Jatuh Tempo Hari Ini
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                            <span class="bg-facebook text-white avatar">
                                <i class="fas fa-exclamation-circle"></i>
                            </span>
                            </div>
                            <div class="col">
                            <div class="font-weight-medium">
                                {{ $menunggak }}
                            </div>
                            <div class="text-muted">
                                Menunggak
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-yellow avatar">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $totalLunas }}</div>
                                <div class="text-muted">Total Lunas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-indigo text-white avatar">
                                    <i class="fas fa-question"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{$jatuhTempoBesok}}</div>
                                <div class="text-muted">Jatuh Tempo Besok</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-cyan text-white avatar">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Rp {{ number_format($pembayaranBulanIni,0,',','.') }}</div>
                                <div class="text-muted">Pembayaran Bulan ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-azure text-white avatar">
                                    <i class="far fa-smile"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{$pelunasanBulanIni}}</div>
                                <div class="text-muted">Pelunasan Bulan ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Outstanding per Cabang
                        </h3>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th>Cabang</th>
                                    <th class="text-end">Outstanding</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outstandingCabang as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_cabang }}</td>
                                    <td class="text-end">Rp {{ number_format($item->outstanding,0,',','.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Outstanding per Marketing
                        </h3>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th>Marketing</th>
                                    <th class="text-end">
                                        Outstanding
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outstandingMarketing as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-end">Rp {{ number_format($item->outstanding,0,',','.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- FILTER --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong>Filter Data</strong>
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                        placeholder="Cari No Pembiayaan / Nama / NIK">
                                </div>
        
                                <div class="col-md-3 mb-2">
                                    <select class="form-select" name="cabang">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangs as $cabang)
                                            <option value="{{ $cabang->id }}"
                                                @selected(request('cabang')==$cabang->id)>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
        
                                <div class="col-md-3 mb-2">
                                    <select class="form-control" name="marketing">
                                        <option value="">Semua Marketing</option>
                                        @foreach($marketings as $marketing)
                                            <option value="{{ $marketing->id }}"
                                                @selected(request('marketing')==$marketing->id)>
                                                {{ $marketing->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="dicairkan" @selected(request('status')=='dicairkan')>Aktif</option>
                                        <option value="lunas" @selected(request('status')=='lunas')>Lunas</option>
                                    </select>
                                </div>
        
                                <div class="col-md-1 mb-2">
                                    <button class="btn btn-primary btn-block">
                                        <i class="fa fa-search"></i>
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        
            {{-- ===================== --}}
            {{-- TABLE --}}
            {{-- ===================== --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong>Daftar Pembiayaan Aktif</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>No Pembiayaan</th>
                                    <th>Nasabah</th>
                                    <th>Marketing</th>
                                    <th>Cabang</th>
                                    <th class="text-right">Plafond</th>
                                    <th class="text-right">Outstanding</th>
                                    <th class="text-center">Progress</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($pembiayaans as $item)
                                    @php
                                        $total = $item->angsurans->count();
                                        $lunas = $item->angsurans->where('status','dibayar')->count();
                                        $progress = $total > 0 ? ($lunas/$total)*100 : 0;
                                        $outstanding = $item->angsurans
                                        ->whereIn('status',['belum_jatuh_tempo','jatuh_tempo'])
                                        ->sum('sisa_tagihan');
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration + ($pembiayaans->firstItem()-1) }}</td>
                                        <td><strong>{{ $item->nomor_pembiayaan }}</strong></td>
                                        <td>{{ $item->pengajuan->nasabah->nama }}</td>
                                        <td>{{ $item->pengajuan->marketing->name }}</td>
                                        <td>{{ $item->pengajuan->cabang->nama }}</td>
                                        <td class="text-right">Rp {{ number_format($item->plafond,0,',','.') }}</td>
                                        <td class="text-right">Rp {{ number_format($outstanding,0,',','.') }}</td>
                                        <td width="180">
                                            <div class="progress">
                                                <div class="progress-bar bg-success" style="width:{{ $progress }}%"></div>
                                            </div>
                                            <small>{{ $lunas }}/{{ $total }}Angsuran</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{ ucfirst($item->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('operasional.show', $item->id) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i>Detail</a>
                                            
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
                        {{ $pembiayaans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection

@section('scripts')

<script>

</script>
@endsection