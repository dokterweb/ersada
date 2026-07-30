@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                    Data Karyawan
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('pelunasan.create',$pembiayaan) }}" class="btn btn-danger">
                            <i class="fas fa-check-circle"></i>
                            Pelunasan
                        </a>
                    </div>
                </div>
            </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="card">
                    <div class="card-header bg-blue-lt">
                        <h3 class="card-title">Data Pengajuan Pembiayaan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-1">{{ $pembiayaan->nomor_pembiayaan }}</h3>
                                <h5 class="text-muted mb-0">{{ $pembiayaan->pengajuan->nasabah->nama }}</h5>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-success fs-6">
                                    {{ strtoupper($pembiayaan->status) }}
                                </span>
                                <br><br>
                                <a href="{{ route('operasional.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                $totalAngsuran = $pembiayaan->angsurans->count();
                $dibayar = $pembiayaan->angsurans->where('status','dibayar')->count();
                $sisa = $totalAngsuran-$dibayar;
                $progress = $totalAngsuran>0? round(($dibayar/$totalAngsuran)*100):0;
        
                $outstanding = $pembiayaan->angsurans
                    ->whereIn('status',[
                        'belum_jatuh_tempo',
                        'jatuh_tempo'
                    ])
                    ->sum('sisa_tagihan');
        
            @endphp
            {{-- ================= SUMMARY ================= --}}
            <div class="row mt-3">

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
                                    <div class="font-weight-medium">Rp {{ number_format($outstanding,0,',','.') }}</div>
                                    Outstanding
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
                                    <div class="font-weight-medium">{{ $dibayar }}</div>
                                    <div class="text-muted">Sudah Dibayar</div>
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
                                    <div class="font-weight-medium">{{ $sisa }}</div>
                                    <div class="text-muted">Sisa</div>
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
                                    <div class="font-weight-medium">{{ $progress }}%</div>
                                    <div class="text-muted">Progress</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= TAB ================= --}}

            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="operasionalTab" data-bs-toggle="tabs">
                        <li class="nav-item">
                          <a href="#informasi" class="nav-link active" data-bs-toggle="tab">Informasi</a>
                        </li>
                        <li class="nav-item">
                          <a href="#jadwal" class="nav-link" data-bs-toggle="tab">Jadwal Angsuran</a>
                        </li>
                        <li class="nav-item">
                          <a href="#pelunasan" class="nav-link" data-bs-toggle="tab">Pelunasan</a>
                        </li>
                        <li class="nav-item">
                          <a href="#pembayaran" class="nav-link" data-bs-toggle="tab">Riwayat Pembayaran</a>
                        </li>
                        <li class="nav-item">
                          <a href="#audit" class="nav-link" data-bs-toggle="tab">Audit Trail</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        {{-- INFORMASI --}}
                        <div class="tab-pane active show" id="informasi">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            Data Nasabah
                                        </div>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="35%">Nama</th>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->nama }}</td>
                                            </tr>
                                            <tr>
                                                <th>NIK</th>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->nik }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alamat</th>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->alamat }}</td>
                                            </tr>
                                            <tr>
                                                <th>Marketing</th>
                                                <td>{{ $pembiayaan->pengajuan->marketing->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Cabang</th>
                                                <td>{{ $pembiayaan->pengajuan->cabang->nama_cabang }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header">Data Pembiayaan</div>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Plafond</th>
                                                <td>Rp {{ number_format($pembiayaan->plafond,0,',','.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tenor</th>
                                                <td>{{ $pembiayaan->tenor }} Bulan</td>
                                            </tr>
                                            <tr>
                                                <th>Bunga</th>
                                                <td>{{ $pembiayaan->persen_bunga }}%</td>
                                            </tr>
                                            <tr>
                                                <th>Administrasi</th>
                                                <td>Rp {{ number_format($pembiayaan->biaya_administrasi,0,',','.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Akad</th>
                                                <td>{{ optional($pembiayaan->akad)->tanggal_akad?->format('d-m-Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Pencairan</th>
                                                <td>{{ optional(optional($pembiayaan->akad)->pencairan)->tanggal_pencairan?->format('d-m-Y') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- JADWAL --}}
                        <div class="tab-pane" id="jadwal">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Jatuh Tempo</th>
                                            <th class="text-end">Pokok</th>
                                            <th class="text-end">Bunga</th>
                                            <th class="text-end">Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($pembiayaan->angsurans as $angsuran)
                                        <tr>
                                            <td>{{ $angsuran->angsuran_ke }}</td>
                                            <td>{{ $angsuran->tanggal_jatuh_tempo->format('d-m-Y') }}</td>
                                            <td class="text-end">{{ number_format($angsuran->pokok_angsuran,0,',','.') }}</td>
                                            <td class="text-end">{{ number_format($angsuran->bunga_angsuran,0,',','.') }}</td>
                                            <td class="text-end">{{ number_format($angsuran->total_angsuran,0,',','.') }}</td>
                                            <td>{{ ucfirst(str_replace('_',' ',$angsuran->status)) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- PELUNASAN --}}
                        <div class="tab-pane" id="pelunasan">
                            <div class="alert alert-info mb-0">
                                @if($pembiayaan->pelunasan)
                                <table class="table table-bordered">
                                    <tr>
                                        <th>No Pelunasan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td>{{ $pembiayaan->pelunasan->nomor_pelunasan }}</td>
                                        <td>{{ $pembiayaan->pelunasan->tanggal_pelunasan->format('d-m-Y') }}</td>
                                        <td>Rp {{ number_format($pembiayaan->pelunasan->total_pelunasan,0,',','.') }}</td>
                                        <td>
                                            <a href="{{ route('pelunasan.show',$pembiayaan->pelunasan) }}" class="btn btn-sm btn-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                @else
                                    <div class="alert alert-info">Belum ada pelunasan.</div>
                                @endif
                            </div>
                        </div>
                        {{-- PEMBAYARAN --}}
                        <div class="tab-pane" id="pembayaran">
                            <div class="alert alert-info mb-0">
                                Riwayat pembayaran akan dibuat pada Sprint berikutnya.
                            </div>
                        </div>
                        {{-- AUDIT --}}
                        <div class="tab-pane" id="audit">
                            <div class="alert alert-secondary mb-0">
                                @forelse($pembiayaan->auditTrails->sortByDesc('created_at') as $audit)
                                <div class="card mb-3 border-start border-4 border-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1">
                                                    @php
                                                        $badge = match($audit->modul){
                                                            'Pembiayaan' => 'primary',
                                                            'Akad' => 'info',
                                                            'Pencairan' => 'warning',
                                                            'Angsuran' => 'success',
                                                            'Pelunasan' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badge }}">
                                                        {{ $audit->modul }}
                                                    </span>
                                                    {{ $audit->aktivitas }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $audit->created_at->format('d-m-Y H:i') }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <strong>
                                                    {{ optional($audit->user)->name }}
                                                </strong>
                                            </div>
                                        </div>
                                        @if($audit->keterangan)
                                            <hr>
                                            <div>
                                                {{ $audit->keterangan }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">Belum ada Audit Trail.</div>
                            @endforelse
                            </div>
                        </div>
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