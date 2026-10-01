@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Detail Nasabah
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
                        <li class="nav-item">
                          <a href="#jaminan" class="nav-link" data-bs-toggle="tab">Jaminan</a>
                        </li>
                        <li class="nav-item">
                          <a href="#analisa" class="nav-link" data-bs-toggle="tab">Analisa Kapital</a>
                        </li>
                        <li class="nav-item">
                          <a href="#survey" class="nav-link" data-bs-toggle="tab">Survey</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        {{-- INFORMASI --}}
                        <div class="tab-pane active show" id="informasi">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            Data Nasabah
                                        </div>
                                        <table class="table table-sm">
                                            <tr>
                                                <td width="35%">Nama</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td>NIK</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->nik }}</td>
                                            </tr>
                                            <tr>
                                                <td>Alamat</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->alamat }}</td>
                                            </tr>
                                            <tr>
                                                <td>Marketing</td>
                                                <td>{{ $pembiayaan->pengajuan->marketing->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Cabang</td>
                                                <td>{{ $pembiayaan->pengajuan->cabang->nama_cabang }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tempat Lahir / Tgl Lahir</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->tempat_lahir.' / '.
                                                \Carbon\Carbon::parse($pembiayaan->pengajuan->nasabah->tgl_lahir)->format('d-m-Y')  }}</td>
                                            </tr>
                                            <tr>
                                                <td>status_perkawinan</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->status_perkawinan }}</td>
                                            </tr>
                                            <tr>
                                                <td>jumlah_tanggungan</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->jumlah_tanggungan }}</td>
                                            </tr>
                                            <tr>
                                                <td>Status Rumah</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->status_rumah }}</td>
                                            </tr>
                                            <tr>
                                                <td>Lama Menetap Tahun</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->lama_menetap_tahun }} tahun</td>
                                            </tr>
                                            <tr>
                                                <td>Lama Menetap Bulan</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->lama_menetap_bulan }} bulan</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            Data Pekerjaan Nasabah
                                        </div>
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Jenis Pekerjaan</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->jenis_pekerjaan }}</td>
                                            </tr>
                                            <tr>
                                                <td>Penghasilan</td>
                                                <td>{{ number_format($pembiayaan->pengajuan->nasabah->pekerjaanNasabah->penghasilan) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama Usaha</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->nama_usaha }}</td>
                                            </tr>
                                            <tr>
                                                <td>Jenis Usaha</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->jenis_usaha }}</td>
                                            </tr>
                                            <tr>
                                                <td>Lama Usaha</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->lama_usaha }}</td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah Pegawai</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->jumlah_pegawai }}</td>
                                            </tr>
                                            <tr>
                                                <td>Alamat Usaha</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->alamat_usaha }}</td>
                                            </tr>
                                            <tr>
                                                <td>Telp Usaha</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->telpon_usaha }}</td>
                                            </tr>
                                            <tr>
                                                <td>Bangunan Usaha</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->bangunan_usaha }}</td>
                                            </tr>
                                            <tr>
                                                <td>Status Tempat Usaha</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->status_tempat_usaha }}</td>
                                            </tr>
                                            <tr>
                                                <td>Aktifitas Usaha</td>
                                                <td>{{ $pembiayaan->pengajuan->nasabah->pekerjaanNasabah->aktivitas_usaha }}</td>
                                            </tr>
                                       
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-outline card-success">
                                        <div class="card-header">Data Pembiayaan</div>
                                        <table class="table table-sm">
                                            <tr>
                                                <td width="40%">Plafond</td>
                                                <td>Rp {{ number_format($pembiayaan->plafond,0,',','.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tenor</td>
                                                <td>{{ $pembiayaan->tenor }} Bulan</td>
                                            </tr>
                                            <tr>
                                                <td>Bunga</td>
                                                <td>{{ $pembiayaan->persen_bunga }}%</td>
                                            </tr>
                                            <tr>
                                                <td>Administrasi</td>
                                                <td>Rp {{ number_format($pembiayaan->biaya_administrasi,0,',','.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Akad</td>
                                                <td>{{ optional($pembiayaan->akad)->tanggal_akad?->format('d-m-Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Pencairan</td>
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
                           {{-- JAMINAN --}}
                        <div class="tab-pane" id="jaminan">
                            @if($pembiayaan->pengajuan->jaminanPengajuans->count())
                                @foreach(
                                    $pembiayaan->pengajuan->jaminanPengajuans
                                    as $index => $jaminan
                                )

                                    <div class="card mb-4 border">

                                        {{-- =========================================================
                                            HEADER JAMINAN
                                        ========================================================== --}}

                                        <div class="card-header bg-light">

                                            <div class="d-flex justify-content-between align-items-center">

                                                <strong>
                                                    Jaminan {{ $index + 1 }} -
                                                </strong>

                                                <span class="badge bg-primary">
                                                    {{ $jaminan->jenis_jaminan }}
                                                </span>

                                            </div>

                                        </div>


                                        {{-- =========================================================
                                            BODY JAMINAN
                                        ========================================================== --}}

                                        <div class="card-body">

                                            <div class="row">


                                                {{-- =====================================================
                                                    DATA UTAMA JAMINAN
                                                ====================================================== --}}

                                                <div class="col-md-6">

                                                    @if(
                                                        in_array(
                                                            $jaminan->jenis_jaminan,
                                                            ['BPKB Motor', 'BPKB Mobil']
                                                        )
                                                    )

                                                        <h3 class="text-primary mb-3">
                                                            Data Kendaraan
                                                        </h3>

                                                        <div class="card mt-2 p-2">

                                                            <table class="table table-sm">

                                                                <tr>
                                                                    <td>
                                                                        Jenis Kendaraan
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->jenis_kendaraan ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        Tahun
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->tahun_kendaraan ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        Merk
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->merk_kendaraan ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        Plat Kendaraan
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->plat_polisi ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        BPKB Atas Nama
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->bpkb_atas_nama ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        No BPKB
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->no_bpkb ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        No Rangka
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->no_rangka ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        No Mesin
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->no_mesin ?? '-' }}
                                                                    </td>
                                                                </tr>


                                                                {{-- BPKB --}}

                                                                <tr>

                                                                    <td>
                                                                        BPKB
                                                                    </td>

                                                                    <td>

                                                                        @if($jaminan->bpkb_status === 'ada')

                                                                            <span class="badge bg-success">
                                                                                Ada
                                                                            </span>

                                                                        @elseif($jaminan->bpkb_status === 'tidak_ada')

                                                                            <span class="badge bg-danger">
                                                                                Tidak Ada
                                                                            </span>

                                                                        @else

                                                                            -

                                                                        @endif

                                                                    </td>

                                                                </tr>


                                                                {{-- PAJAK STNK --}}

                                                                <tr>

                                                                    <td>
                                                                        Pajak STNK
                                                                    </td>

                                                                    <td>

                                                                        @if($jaminan->pajak_stnk_status === 'ada')

                                                                            <span class="badge bg-success">
                                                                                Ada
                                                                            </span>

                                                                        @elseif($jaminan->pajak_stnk_status === 'tidak_ada')

                                                                            <span class="badge bg-danger">
                                                                                Tidak Ada
                                                                            </span>

                                                                        @else

                                                                            -

                                                                        @endif

                                                                    </td>

                                                                </tr>


                                                                {{-- STATUS PAJAK --}}

                                                                <tr>

                                                                    <td>
                                                                        Status Pajak
                                                                    </td>

                                                                    <td>

                                                                        @if($jaminan->status_pajak === 'hidup')

                                                                            <span class="badge bg-success">
                                                                                Hidup
                                                                            </span>

                                                                        @elseif($jaminan->status_pajak === 'mati')

                                                                            <span class="badge bg-danger">
                                                                                Mati
                                                                            </span>

                                                                        @else

                                                                            -

                                                                        @endif

                                                                    </td>

                                                                </tr>

                                                            </table>

                                                        </div>


                                                    @elseif(
                                                        $jaminan->jenis_jaminan === 'Surat Tanah'
                                                    )

                                                        <h3 class="text-primary mb-3">
                                                            Data Surat Tanah
                                                        </h3>

                                                        <div class="card mt-2 p-2">

                                                            <table class="table table-sm">

                                                                <tr>

                                                                    <td style="width:50%">
                                                                        SKT / SPGR
                                                                    </td>

                                                                    <td>

                                                                        @if(
                                                                            $jaminan->skt_spgr_status === 'ada'
                                                                        )

                                                                            <span class="badge bg-success">
                                                                                Ada
                                                                            </span>

                                                                        @elseif(
                                                                            $jaminan->skt_spgr_status === 'tidak_ada'
                                                                        )

                                                                            <span class="badge bg-danger">
                                                                                Tidak Ada
                                                                            </span>

                                                                        @else

                                                                            -

                                                                        @endif

                                                                    </td>

                                                                </tr>


                                                                <tr>

                                                                    <td>
                                                                        SKT / SPGR
                                                                        Dikeluarkan Oleh
                                                                    </td>

                                                                    <td>
                                                                        {{ $jaminan->skt_spgr_dikeluarkan_oleh ?? '-' }}
                                                                    </td>

                                                                </tr>


                                                                <tr>

                                                                    <td>
                                                                        Sertifikat
                                                                    </td>

                                                                    <td>

                                                                        @if(
                                                                            $jaminan->sertifikat_status === 'ada'
                                                                        )

                                                                            <span class="badge bg-success">
                                                                                Ada
                                                                            </span>

                                                                        @elseif(
                                                                            $jaminan->sertifikat_status === 'tidak_ada'
                                                                        )

                                                                            <span class="badge bg-danger">
                                                                                Tidak Ada
                                                                            </span>

                                                                        @else

                                                                            -

                                                                        @endif

                                                                    </td>

                                                                </tr>

                                                            </table>

                                                        </div>


                                                    @else

                                                        <div class="alert alert-secondary">

                                                            Belum ada detail khusus
                                                            untuk jenis jaminan ini.

                                                        </div>

                                                    @endif

                                                </div>


                                                {{-- =====================================================
                                                    NILAI TAKSIRAN & DOKUMEN
                                                ====================================================== --}}

                                                <div class="col-md-6">

                                                    <div class="card mt-2 p-2">

                                                        <table class="table table-sm">

                                                            <tr>

                                                                <th style="width:25%">
                                                                    Nilai Taksiran
                                                                </th>

                                                                <td>
                                                                    Rp
                                                                    {{ number_format(
                                                                        $jaminan->nilai_taksiran ?? 0,
                                                                        0,
                                                                        ',',
                                                                        '.'
                                                                    ) }}
                                                                </td>

                                                            </tr>


                                                            <tr>

                                                                <td>
                                                                    Deskripsi
                                                                </td>

                                                                <td>
                                                                    {{ $jaminan->detail_jaminan ?? '-' }}
                                                                </td>

                                                            </tr>

                                                        </table>


                                                        {{-- =================================================
                                                            DOKUMEN JAMINAN
                                                        ================================================== --}}

                                                        <h5 class="text-primary mb-3">
                                                            Dokumen Jaminan
                                                        </h5>


                                                        @if(
                                                            $jaminan->dokumenJaminans->count()
                                                        )

                                                            <div class="table-responsive">

                                                                <table class="table table-sm">

                                                                    @foreach(
                                                                        $jaminan->dokumenJaminans
                                                                        as $dokumen
                                                                    )

                                                                        <tr>

                                                                            <td>
                                                                                {{ $dokumen->nama_file }}
                                                                            </td>

                                                                            <td>

                                                                                <a
                                                                                    href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-sm btn-primary"
                                                                                >
                                                                                    Lihat Dokumen
                                                                                </a>

                                                                            </td>

                                                                        </tr>

                                                                    @endforeach

                                                                </table>

                                                            </div>

                                                        @else

                                                            <div class="alert alert-secondary mb-0">

                                                                Belum ada dokumen jaminan.

                                                            </div>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @endforeach
                            @else
                                <div class="card mb-4">

                                    <div class="card-header bg-warning">

                                        <h3 class="card-title mb-0">
                                            Data Jaminan
                                        </h3>

                                    </div>

                                    <div class="card-body">

                                        <div class="alert alert-warning mb-0">

                                            Belum ada data jaminan.

                                        </div>

                                    </div>

                                </div>
                            @endif
                        </div>
                        {{-- ANALISA --}}
                         <div class="tab-pane" id="analisa">
                            <div class="card">
                            <div class="card-body">
                                <div class="row justify-content-center">
                                        <div class="col-10">
                                        @php
                                            $kapital = $pembiayaan->pengajuan->kapital;
                                        @endphp
                                
                                        @if($kapital)

                                            <div class="row">

                                                {{-- PENDAPATAN --}}
                                                <div class="col-md-6">

                                                    <h4 class="text-primary">
                                                        Pendapatan
                                                    </h4>

                                                    <table class="table table-sm">

                                                        {{-- Omzet Usaha --}}
                                                        <tr>
                                                            <td>Omzet Harian</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->omzet_harian ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        {{-- Laba Usaha --}}
                                                        <tr>
                                                            <td>Laba Harian</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->laba_harian ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        {{-- Gaji Debitur --}}
                                                        <tr>
                                                            <td>Gaji Debitur</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->gaji_debitur ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        {{-- Pendapatan Pasangan --}}
                                                        <tr>
                                                            <td>Pendapatan Pasangan</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->pendapatan_pasangan ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                    </table>

                                                </div>


                                                {{-- PENGELUARAN --}}
                                                <div class="col-md-6">

                                                    <h4 class="text-danger">
                                                        Pengeluaran
                                                    </h4>

                                                    <table class="table table-sm">

                                                        <tr>
                                                            <td>Rumah Tangga</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->biaya_rumah_tangga ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>Motor</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->biaya_motor ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>Koperasi</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->biaya_koperasi ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>Angsuran Lain</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->angsuran_lain ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>Kontrak Rumah</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->biaya_kontrak_rumah ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>Tempat Usaha</td>
                                                            <td class="text-end">
                                                                Rp {{ number_format($kapital->biaya_tempat_usaha ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                    </table>

                                                </div>

                                            </div>


                                            <hr>


                                            <div class="row">

                                                <div class="col-md-6">

                                                    <h4>
                                                        Total Pengeluaran
                                                    </h4>

                                                    <h2 class="text-danger">
                                                        Rp {{ number_format($kapital->total_pengeluaran ?? 0, 0, ',', '.') }}
                                                    </h2>

                                                </div>


                                                <div class="col-md-6 text-end">

                                                    <h4>
                                                        Sisa Pendapatan
                                                    </h4>

                                                    <h2 class="text-success">
                                                        Rp {{ number_format($kapital->sisa_pendapatan ?? 0, 0, ',', '.') }}
                                                    </h2>

                                                </div>

                                            </div>


                                        @else

                                            <div class="alert alert-warning">
                                                Data Analisa Kapital belum diisi.
                                            </div>

                                        @endif
                                        </table>
                                        </div>
                                </div>
                                </div>
                            </div>
                        </div>
                         {{-- JADWAL --}}
                        <div class="tab-pane" id="survey">
                            @if($survey)
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-sm">
                            <tr>
                                <td>Status</td>
                                <td>
                                    @php
                                    $statusClass = match($survey->status) {
                                        'submitted' => 'bg-success',
                                        'reviewed' => 'bg-primary',
                                        'revision' => 'bg-warning text-dark',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($survey->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Surveyor</td>
                                <td>
                                {{ $survey->assignedTo?->name ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Mulai Survey</td>
                                <td>
                                    {{ $survey->started_at
                                        ? \Carbon\Carbon::parse($survey->started_at)->format('d/m/Y H:i')
                                        : '-'
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td>Selesai Survey</td>
                                <td>
                                    {{ $survey->finished_at
                                        ? \Carbon\Carbon::parse($survey->finished_at)->format('d/m/Y H:i')
                                        : '-'
                                    }}
                                </td>
                            </tr>
                        </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($rumah->count())
                            <hr>
                                <h4 class="mb-2">Dokumentasi Rumah</h4>
                                <div class="row">

                                    @foreach($rumah as $item)
                                        <div class="col-md-3 mb-4">
                                            <div class="card h-100">
                                                <a href="{{ asset('storage/'.$item->file) }}" target="_blank">
                                                    <img src="{{ asset('storage/'.$item->file) }}" class="card-img-top"
                                                        style="height:250px; object-fit:cover;" alt="Dokumentasi Rumah">
                                                </a>
                                                <div class="card-body">
                                                    <strong>
                                                        {{ ucfirst($item->posisi ?? 'Dokumentasi') }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0">Tidak ada dokumentasi rumah.</div>
                            @endif

                            @if($usaha->count())
                            <hr>
                            <div class="row">
                                @foreach($usaha as $item)
                                    <div class="col-md-3 mb-4">
                                        <div class="card h-100">
                                            <a href="{{ asset('storage/'.$item->file) }}" target="_blank">
                                                <img src="{{ asset('storage/'.$item->file) }}" class="card-img-top"
                                                    style="height:250px; object-fit:cover;" alt="Dokumentasi Usaha">
                                            </a>
                                            <div class="card-body">
                                                <strong>Dokumentasi Usaha</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @else
                                <div class="alert alert-secondary mb-0">Tidak ada dokumentasi usaha.</div>
                            @endif

                            @if($videos->count())
                            <h4 class="mb-2">Video Survey</h4>
                            <div class="row">
                                @foreach($videos as $video)
                                    <div class="col-md-6 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <video controls class="w-100 rounded" style="max-height:500px;">
                                                    <source src="{{ asset('storage/'.$video->file) }}" type="video/mp4">
                                                    Browser tidak mendukung video.
                                                </video>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-secondary mb-0">
                                Tidak ada video survey.
                            </div>
                        @endif
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