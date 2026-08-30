@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Create Survey
            </h2>
          </div>
          
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-blue-lt">
                    <h3 class="card-title">Data Pengajuan Pembiayaan</h3>
                </div>
                <div class="card-body">
                
                    <div class="row">
                        <div class="col-md-4">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="220">No Pengajuan</th>
                                    <td>{{ $pengajuan->nomor_pengajuan }}</td>
                                </tr>
                                <tr>
                                    <th>Nasabah</th>
                                    <td>{{ optional($pengajuan->nasabah)->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Marketing</th>
                                    <td>{{ optional($pengajuan->marketing->user)->name }}</td>
                                </tr>
                                <tr>
                                    <th>Cabang</th>
                                    <td>{{ optional($pengajuan->cabang)->nama_cabang }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-bordered">
                                <tr>
                                    <td>Tanggal Pengajuan</td>
                                    <td>{{ $pengajuan->tanggal_pengajuan }}</td>
                                </tr>
                                <tr>
                                    <td>Nominal</td>
                                    <td>{{ number_format($pengajuan->nominal_pengajuan) }}</td>
                                </tr>
                                <tr>
                                    <td>Tenor</td>
                                    <td>{{ $pengajuan->tenor }} Bulan</td>
                                </tr>
                                <tr>
                                    <td>Pekerjaan</td>
                                    <td>{{ $pengajuan->kategori_nasabah }}</td>
                                </tr>
                                <tr>
                                    <td>Tujuan Pinjaman</td>
                                    <td>{{ $pengajuan->tujuan_pinjaman }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4 text-center">

                                <strong>Foto Nasabah</strong>

                                <div class="mt-2">

                                    @if($pengajuan->nasabah?->foto_nasabah)

                                        <img
                                            src="{{ asset('storage/' . $pengajuan->nasabah->foto_nasabah) }}"
                                            class="img-thumbnail"
                                            style="max-width:180px; max-height:220px;"
                                        >

                                    @else

                                        <div class="text-muted">
                                            Foto belum tersedia
                                        </div>

                                    @endif

                                </div>

                            </div>                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {{-- DATA NASABAH --}}
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h3 class="card-title mb-0">
                                        Data Nasabah
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="220">Nama Nasabah</th>
                                            <td>{{ $pengajuan->nasabah?->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td>{{ $pengajuan->nasabah?->nik ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. HP</th>
                                            <td>{{ $pengajuan->nasabah?->no_hp ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tempat / Tanggal Lahir</th>
                                            <td>
                                                 {{ $pengajuan->nasabah?->tempat_lahir ?? '-' }}
                                                /
                                                {{ $pengajuan->nasabah?->tgl_lahir ?? '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status Perkawinan</th>
                                            <td>{{ $pengajuan->nasabah?->status_perkawinan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jlh Tanggungan</th>
                                            <td>{{ $pengajuan->nasabah?->jumlah_tanggungan ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Rumah</th>
                                            <td>{{ $pengajuan->nasabah?->status_rumah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $pengajuan->nasabah?->alamat ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            {{-- DATA PEKERJAAN --}}
                            @php
                                $pekerjaan = $pengajuan->nasabah?->pekerjaanNasabah;
                            @endphp
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title mb-0">
                                        Data Pekerjaan
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="220">Jenis Pekerjaan</th>
                                            <td>{{ $pekerjaan?->jenis_pekerjaan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Penghasilan</th>
                                            <td>Rp {{ number_format($pekerjaan?->penghasilan ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Usaha</th>
                                            <td>{{ $pekerjaan?->nama_usaha ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Usaha</th>
                                            <td>{{ $pekerjaan?->jenis_usaha ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lama Usaha</th>
                                            <td>{{ $pekerjaan?->lama_usaha ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jlh Pegawai</th>
                                            <td>{{ $pekerjaan?->jumlah_pegawai ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat Usaha</th>
                                            <td>{{ $pekerjaan?->alamat_usaha ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Tempat Usaha</th>
                                            <td>{{ $pekerjaan?->status_tempat_usaha ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Aktifitas Usaha</th>
                                            <td>{{ $pekerjaan?->aktivitas_usaha ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                   @if($pasangan)
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-yellow text-yellow-fg">
                                <h3 class="card-title">DATA PASANGAN NASABAH DAN PEKERJAAN</h3>
                            </div>
                            <div class="card-body">
                            <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mt-3 p-3">
                                            <h2>Data Pasangan Nasabah</h2>
                                            <table class="table">
                                                <tr>
                                                    <td>Nama</td>
                                                    <td>{{ $pasangan->nama }}</td>
                                                </tr>
                                                <tr>
                                                    <td>No HP</td>
                                                    <td>{{ $pasangan->no_hp }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tempat Lahir</td>
                                                    <td>{{ $pasangan->tempat_lahir }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Lahir</td>
                                                    {{-- <td>{{ $pasangan->tgl_lahir }}</td> --}}
                                                    <td>{{ \Carbon\Carbon::parse($pasangan->tgl_lahir)->format('d-m-Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat</td>
                                                    <td>{{ $pasangan->alamat }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mt-3 p-3">
                                            <h2>Data Pekerjaan Nasabah</h2>
                                            @if($pasangan && $pasangan->pekerjaan)
                                            <table class="table">
                                                <tr>
                                                    <td>Jenis Pekerjaan</td>
                                                    <td>{{ $pasangan->pekerjaan->jenis_pekerjaan }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Penghasilan</td>
                                                    <td>{{ number_format($pasangan->pekerjaan->penghasilan) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Nama Usaha</td>
                                                    <td>{{ $pasangan->pekerjaan->nama_usaha }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Jenis Usaha</td>
                                                    <td>{{ $pasangan->pekerjaan->jenis_usaha }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Lama Usaha</td>
                                                    <td>{{ $pasangan->pekerjaan->lama_usaha }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Jumlah Pegawai</td>
                                                    <td>{{ $pasangan->pekerjaan->jumlah_pegawai }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat Usaha</td>
                                                    <td>{{ $pasangan->pekerjaan->alamat_usaha }}</td>
                                                </tr>                                       
                                            </table>
                                            @else
                                            <h2>Tidak Bekerja</h2>
                                            @endif
                                        </div>
                                    </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    @endif

                     @if($penjamin)
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-cyan text-cyan-fg">
                                <h3 class="card-title">DATA PENJAMIN DAN PEKERJAAN</h3>
                            </div>
                            <div class="card-body">
                            <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mt-3 p-3">
                                            <h2>Data Penjamin</h2>
                                            <table class="table">
                                                <tr>
                                                    <td>Nama</td>
                                                    <td>{{ $penjamin->nama }}</td>
                                                </tr>
                                                <tr>
                                                    <td>No HP</td>
                                                    <td>{{ $penjamin->no_hp }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tempat Lahir</td>
                                                    <td>{{ $penjamin->tempat_lahir }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Lahir</td>
                                                    {{-- <td>{{ $penjamin->tgl_lahir }}</td> --}}
                                                    <td>{{ \Carbon\Carbon::parse($penjamin->tgl_lahir)->format('d-m-Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat</td>
                                                    <td>{{ $penjamin->alamat }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mt-3 p-3">
                                            <h2>Data Pekerjaan Penjamin</h2>
                                            @if($penjamin && $penjamin->pekerjaan)
                                            <table class="table">
                                                <tr>
                                                    <td>Jenis Pekerjaan</td>
                                                    <td>{{ $penjamin->pekerjaan->jenis_pekerjaan }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Penghasilan</td>
                                                    <td>{{ number_format($penjamin->pekerjaan->penghasilan) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Nama Usaha</td>
                                                    <td>{{ $penjamin->pekerjaan->nama_usaha }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Jenis Usaha</td>
                                                    <td>{{ $penjamin->pekerjaan->jenis_usaha }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Lama Usaha</td>
                                                    <td>{{ $penjamin->pekerjaan->lama_usaha }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Jumlah Pegawai</td>
                                                    <td>{{ $penjamin->pekerjaan->jumlah_pegawai }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat Usaha</td>
                                                    <td>{{ $penjamin->pekerjaan->alamat_usaha }}</td>
                                                </tr>                                       
                                            </table>
                                            @else
                                            <h2>Tidak Bekerja</h2>
                                            @endif
                                        </div>
                                    </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- DATA SAUDARA --}}
                    @if($saudaras->count())
                    <div class="card mb-4">

                        <div class="card-header bg-secondary text-white">
                            <h3 class="card-title mb-0">
                                Data Saudara
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Hubungan</th>
                                            <th>No. HP</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach($saudaras as $index => $saudara)

                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $saudara->nama }}</td>
                                            <td>{{ $saudara->hubungan ?? '-' }}</td>
                                            <td>{{ $saudara->no_hp ?? '-' }}</td>
                                            <td>{{ $saudara->alamat ?? '-' }}</td>
                                        </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>
                    @endif

                    {{-- DOKUMEN --}}
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h3 class="card-title mb-0">
                                Dokumen Pengajuan
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Jenis Dokumen</th>
                                            <th>Nama File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pengajuan->dokumenPengajuans as $dokumen)
                                        <tr>
                                            <td>{{ ucwords(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}</td>
                                            <td>{{ $dokumen->nama_file }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $dokumen->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ asset('storage/' . $dokumen->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-primary">
                                                    Lihat
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                Belum ada dokumen.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                        {{-- JAMINAN --}}
                    @if($pengajuan->jaminanPengajuans->count())
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header bg-warning">
                                <h3 class="card-title mb-0">Data Jaminan</h3>
                            </div>
                            <div class="card-body">
                                @foreach($pengajuan->jaminanPengajuans as $index => $jaminan)
                                    <div class="card mb-4 border">
                                        {{-- HEADER JAMINAN --}}
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
                                        <div class="card-body">
                                                {{-- BPKB MOTOR / MOBIL --}}
                                            @if(
                                                in_array($jaminan->jenis_jaminan,['BPKB Motor', 'BPKB Mobil'])
                                            )
                                                <h3 class="text-primary mb-3">Data Kendaraan</h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card mt-2 p-2">
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td>Jenis Kendaraan</td>
                                                                    <td>{{ $jaminan->jenis_kendaraan ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tahun</td>
                                                                    <td>{{ $jaminan->tahun_kendaraan ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Merk</td>
                                                                    <td>{{ $jaminan->merk_kendaraan ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Plat Kendaraan</td>
                                                                    <td>{{ $jaminan->plat_polisi ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>BPKB Atas Nama</td>
                                                                    <td>
                                                                        {{ $jaminan->bpkb_atas_nama ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>No BPKB</td>
                                                                    <td>
                                                                    {{ $jaminan->no_bpkb ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>No Rangka</td>
                                                                    <td>
                                                                    {{ $jaminan->no_rangka ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>No Mesin</td>
                                                                    <td>
                                                                    {{ $jaminan->no_mesin ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>BPKB</td>
                                                                    <td>
                                                                        @if($jaminan->bpkb_status === 'ada')
                                                                            <span class="badge bg-success">
                                                                                Ada
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-danger">
                                                                                Tidak Ada
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Pajak STNK</td>
                                                                    <td>
                                                                        @if($jaminan->pajak_stnk_status === 'ada')
                                                                            <span class="badge bg-success">
                                                                                Ada
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-danger">
                                                                                Tidak Ada
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Status Pajak</td>
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
                                                    </div>
                                                

                                                {{-- SURAT TANAH --}}
                                            @elseif($jaminan->jenis_jaminan === 'Surat Tanah')
                                                <h3 class="text-primary mb-3">Data Surat Tanah</h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card mt-2 p-2">
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td style="width:50%">SKT / SPGR</td>
                                                                    <td>
                                                                        @if($jaminan->skt_spgr_status === 'ada')
                                                                            <span class="badge bg-success">Ada</span>
                                                                        @elseif($jaminan->skt_spgr_status === 'tidak_ada')
                                                                            <span class="badge bg-danger">Tidak Ada</span>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>SKT / SPGR Dikeluarkan Oleh</td>
                                                                    <td>
                                                                    {{ $jaminan->skt_spgr_dikeluarkan_oleh ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Sertifikat</td>
                                                                    <td>
                                                                    @if($jaminan->sertifikat_status === 'ada')
                                                                            <span class="badge bg-success">Ada</span>
                                                                        @elseif($jaminan->sertifikat_status === 'tidak_ada')
                                                                            <span class="badge bg-danger">Tidak Ada</span>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                
                                            @endif
                                                {{-- NILAI TAKSIRAN --}}
                                                <div class="col-md-6">
                                                    <div class="card mt-2 p-2">
                                                        <table class="table table-sm">
                                                            <tr>
                                                                <th style="width:25%">Nilai Taksiran</th>
                                                                <td>Rp {{ number_format($jaminan->nilai_taksiran ?? 0,0,',','.') }}</td>
                                                                
                                                            </tr>
                                                            <tr>
                                                                <td>Deskripsi</td>
                                                                <td>{{$jaminan->detail_jaminan ?? '-'}}</td>
                                                            </tr>
                                                        </table>
                                                        <h5 class="text-primary mb-3">Dokumen Jaminan</h5>
                                                        @if($jaminan->dokumenJaminans->count())
                                                            <div class="row">
                                                            <table class="table table-sm">
                                                                    @foreach($jaminan->dokumenJaminans as $dokumen)
                                                                    <tr>
                                                                        <td>{{ $dokumen->nama_file }}</td>
                                                                        <td>
                                                                            <a href="{{ asset( 'storage/' . $dokumen->file_path) }}" target="_blank"
                                                                            class="btn btn-sm btn-primary">Lihat Dokumen
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </table>
                                                            </div>
                                                        @else
                                                            <div class="alert alert-secondary">
                                                                Belum ada dokumen jaminan.
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                            {{-- =================================================
                                                DOKUMEN JAMINAN
                                            ================================================== --}}


                                        
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                        </div>
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
                    </div>
                    @endif

                    {{-- ========================================================= --}}
                    {{-- DOKUMEN PAYROLL --}}
                    {{-- ========================================================= --}}

                    <div class="card mb-4">

                        <div class="card-header bg-success text-white">

                            <strong>
                                DOKUMEN TAMBAHAN KARYAWAN / PAYROLL
                            </strong>

                        </div>


                        <div class="card-body">

                            <div class="row">


                                {{-- BPJS --}}

                                <div class="col-md-6 mb-4">

                                    <strong>
                                        1. Kartu Jamsostek /
                                        BPJS Ketenagakerjaan
                                    </strong>


                                    @php

                                        $bpjs =
                                            $pengajuan
                                                ->dokumenPayrolls
                                                ->where(
                                                    'jenis_dokumen',
                                                    'bpjs_ketenagakerjaan'
                                                );

                                    @endphp


                                    @if($bpjs->count())

                                        <div class="list-group mt-2">

                                            @foreach($bpjs as $dokumen)

                                                <div
                                                    class="
                                                        list-group-item
                                                        d-flex
                                                        justify-content-between
                                                        align-items-center
                                                    "
                                                >

                                                    {{ $dokumen->nama_file }}


                                                    <a
                                                        href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        Lihat
                                                    </a>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <div class="text-muted mt-2">
                                            Belum ada dokumen.
                                        </div>

                                    @endif

                                </div>


                                {{-- BUKU TABUNGAN --}}

                                <div class="col-md-6 mb-4">

                                    <strong>
                                        2. Buku Tabungan
                                    </strong>


                                    @php

                                        $tabungan =
                                            $pengajuan
                                                ->dokumenPayrolls
                                                ->where(
                                                    'jenis_dokumen',
                                                    'buku_tabungan'
                                                );

                                    @endphp


                                    @if($tabungan->count())

                                        <div class="list-group mt-2">

                                            @foreach($tabungan as $dokumen)

                                                <div
                                                    class="
                                                        list-group-item
                                                        d-flex
                                                        justify-content-between
                                                        align-items-center
                                                    "
                                                >

                                                    {{ $dokumen->nama_file }}


                                                    <a
                                                        href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        Lihat
                                                    </a>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <div class="text-muted mt-2">
                                            Belum ada dokumen.
                                        </div>

                                    @endif

                                </div>


                                {{-- ATM --}}

                                <div class="col-md-6 mb-4">

                                    <strong>
                                        3. Kartu ATM
                                    </strong>


                                    @php

                                        $atm =
                                            $pengajuan
                                                ->dokumenPayrolls
                                                ->where(
                                                    'jenis_dokumen',
                                                    'atm'
                                                );

                                    @endphp


                                    @if($atm->count())

                                        <div class="list-group mt-2">

                                            @foreach($atm as $dokumen)

                                                <div
                                                    class="
                                                        list-group-item
                                                        d-flex
                                                        justify-content-between
                                                        align-items-center
                                                    "
                                                >

                                                    {{ $dokumen->nama_file }}


                                                    <a
                                                        href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        Lihat
                                                    </a>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <div class="text-muted mt-2">
                                            Belum ada dokumen.
                                        </div>

                                    @endif

                                </div>


                                {{-- SLIP GAJI --}}

                                <div class="col-md-6 mb-4">

                                    <strong>
                                        4. Slip Gaji
                                    </strong>


                                    @php

                                        $slipGaji =
                                            $pengajuan
                                                ->dokumenPayrolls
                                                ->where(
                                                    'jenis_dokumen',
                                                    'slip_gaji'
                                                );

                                    @endphp


                                    @if($slipGaji->count())

                                        <div class="list-group mt-2">

                                            @foreach(
                                                $slipGaji
                                                as $dokumen
                                            )

                                                <div
                                                    class="
                                                        list-group-item
                                                        d-flex
                                                        justify-content-between
                                                        align-items-center
                                                    "
                                                >

                                                    {{ $dokumen->nama_file }}


                                                    <a
                                                        href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        Lihat
                                                    </a>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <div class="text-muted mt-2">
                                            Belum ada dokumen.
                                        </div>

                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- ANALISA --}}
                    <div class="card mb-4">

                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">
                                Analisa Pengajuan
                            </h3>
                        </div>

                        <div class="card-body">

                            @php
                                $analisa = $pengajuan->analisa;
                            @endphp
                            @if($analisa)
                                <div class="row">

                                    {{-- ========================================= --}}
                                    {{-- KOLOM KIRI --}}
                                    {{-- ========================================= --}}
                                    <div class="col-md-4">

                                        {{-- Harga Kredit --}}
                                        <div class="mb-4">

                                            <strong>Harga Kredit</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->harga_kredit,
                                                'expected' => 1,
                                                'label' => 'Setuju'
                                            ])

                                            &nbsp;&nbsp;&nbsp;

                                            @include('layouts.checklist', [
                                                'value' => $analisa->harga_kredit,
                                                'expected' => 0,
                                                'label' => 'Tidak Setuju'
                                            ])

                                        </div>


                                        {{-- Kewajiban Angsuran --}}
                                        <div class="mb-4">

                                            <strong>Kewajiban Angsuran</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->kewajiban_angsuran,
                                                'expected' => 1,
                                                'label' => 'Sudah Dijelaskan'
                                            ])

                                            &nbsp;&nbsp;&nbsp;

                                            @include('layouts.checklist', [
                                                'value' => $analisa->kewajiban_angsuran,
                                                'expected' => 0,
                                                'label' => 'Belum'
                                            ])

                                        </div>


                                        {{-- Status Pemohon --}}
                                        <div class="mb-4">

                                            <strong>Status Pemohon</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->status_pemohon,
                                                'expected' => 'pemilik',
                                                'label' => 'Pemilik'
                                            ])

                                            @include('layouts.checklist', [
                                                'value' => $analisa->status_pemohon,
                                                'expected' => 'karyawan',
                                                'label' => 'Karyawan'
                                            ])

                                            @include('layouts.checklist', [
                                                'value' => $analisa->status_pemohon,
                                                'expected' => 'pengelola',
                                                'label' => 'Pengelola'
                                            ])

                                        </div>

                                    </div>


                                    {{-- ========================================= --}}
                                    {{-- KOLOM TENGAH --}}
                                    {{-- ========================================= --}}
                                    <div class="col-md-4">

                                        {{-- Status Tempat Tinggal --}}
                                        <div class="mb-4">

                                            <strong>Status Tempat Tinggal</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->status_tempat_tinggal,
                                                'expected' => 1,
                                                'label' => 'Milik'
                                            ])

                                            &nbsp;&nbsp;&nbsp;

                                            @include('layouts.checklist', [
                                                'value' => $analisa->status_tempat_tinggal,
                                                'expected' => 0,
                                                'label' => 'Kontrak'
                                            ])

                                        </div>


                                        {{-- Data Pemohon --}}
                                        <div class="mb-4">

                                            <strong>Data Pemohon</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->data_pemohon_lengkap,
                                                'expected' => 1,
                                                'label' => 'Lengkap'
                                            ])

                                            &nbsp;&nbsp;&nbsp;

                                            @include('layouts.checklist', [
                                                'value' => $analisa->data_pemohon_lengkap,
                                                'expected' => 0,
                                                'label' => 'Tidak Lengkap'
                                            ])

                                        </div>


                                        {{-- KTP Pemohon --}}
                                        <div class="mb-4">

                                            <strong>KTP Pemohon</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->ktp_pemohon_valid,
                                                'expected' => 1,
                                                'label' => 'Valid'
                                            ])

                                            &nbsp;&nbsp;&nbsp;

                                            @include('layouts.checklist', [
                                                'value' => $analisa->ktp_pemohon_valid,
                                                'expected' => 0,
                                                'label' => 'Tidak Valid'
                                            ])

                                        </div>

                                    </div>


                                    {{-- ========================================= --}}
                                    {{-- KOLOM KANAN --}}
                                    {{-- ========================================= --}}
                                    <div class="col-md-4">

                                        {{-- KTP Pasangan --}}
                                        <div class="mb-4">

                                            <strong>KTP Pasangan</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->ktp_pasangan_valid,
                                                'expected' => 1,
                                                'label' => 'Valid'
                                            ])

                                            &nbsp;&nbsp;&nbsp;

                                            @include('layouts.checklist', [
                                                'value' => $analisa->ktp_pasangan_valid,
                                                'expected' => 0,
                                                'label' => 'Tidak Valid'
                                            ])

                                        </div>


                                        {{-- Kartu Keluarga --}}
                                        <div class="mb-4">

                                            <strong>Kartu Keluarga</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->kk_valid,
                                                'expected' => 1,
                                                'label' => 'Valid'
                                            ])

                                            &nbsp;&nbsp;&nbsp;

                                            @include('layouts.checklist', [
                                                'value' => $analisa->kk_valid,
                                                'expected' => 0,
                                                'label' => 'Tidak Valid'
                                            ])

                                        </div>


                                        {{-- Perbaikan Plafon --}}
                                        <div class="mb-4">

                                            <strong>Perbaikan Plafon</strong><br>

                                            @include('layouts.checklist', [
                                                'value' => $analisa->perbaikan_plafon,
                                                'expected' => 1,
                                                'label' => 'Sudah'
                                            ])

                                            &nbsp;&nbsp;&nbsp;

                                            @include('layouts.checklist', [
                                                'value' => $analisa->perbaikan_plafon,
                                                'expected' => 0,
                                                'label' => 'Belum'
                                            ])

                                        </div>

                                    </div>

                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    Data analisa belum tersedia.
                                </div>
                            @endif
                        </div>
                    </div>


                    {{-- KAPITAL --}}
                    @if($pengajuan->kapital)

                    <div class="card mb-4">

                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">
                                Analisa Kapital
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <div class="row">

                                        <div class="col-md-6">

                                            <h5 class="text-primary">
                                                Pendapatan
                                            </h5>

                                            <table class="table table-sm">

                                                <tr>
                                                    <td>Omzet Harian</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->omzet_harian ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Laba Harian</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->laba_harian ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Gaji Debitur</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->gaji_debitur ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Pendapatan Pasangan</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->pendapatan_pasangan ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                            </table>

                                        </div>

                                        <div class="col-md-6">

                                            <h5 class="text-danger">
                                                Pengeluaran
                                            </h5>

                                            <table class="table table-sm">

                                                <tr>
                                                    <td>Rumah Tangga</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->biaya_rumah_tangga ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Motor</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->biaya_motor ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Koperasi</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->biaya_koperasi ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Angsuran Lain</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->angsuran_lain ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Kontrak Rumah</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->biaya_kontrak_rumah ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Tempat Usaha</td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pengajuan->kapital->biaya_tempat_usaha ?? 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                            </table>

                                        </div>

                                    </div>
                                    <hr>
                                    <div class="row">

                                        <div class="col-md-6">

                                            <strong>Total Pengeluaran</strong>

                                            <h3 class="text-danger">
                                                Rp {{ number_format($pengajuan->kapital->total_pengeluaran ?? 0, 0, ',', '.') }}
                                            </h3>

                                        </div>

                                        <div class="col-md-6 text-end">

                                            <strong>Sisa Pendapatan</strong>

                                            <h3 class="text-success">
                                                Rp {{ number_format($pengajuan->kapital->sisa_pendapatan ?? 0, 0, ',', '.') }}
                                            </h3>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>
                <form action="{{ route('survey.store',$pengajuan->id) }}" method="POST">
                @csrf
                <div class="card-body">                    

                    <div class="mb-3">

                        <label class="form-label">
                            Jenis Penugasan
                        </label>

                        <select name="jenis" id="jenis" class="form-select">

                            @if($canSurveySelf)

                                <option value="sendiri"
                                    {{ old('jenis', 'sendiri') == 'sendiri' ? 'selected' : '' }}>
                                    Survey Saya Sendiri
                                </option>

                            @endif


                            @if($canAssignSurveyor)

                                <option value="assign"
                                    {{ old('jenis') == 'assign' ? 'selected' : '' }}>
                                    Tugaskan Surveyor
                                </option>

                            @endif

                        </select>

                        @error('jenis')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>



                    <div class="mb-3" id="surveyor-area">

                        <label class="form-label">
                            Pilih Surveyor
                        </label>

                        <select name="surveyor_id" id="surveyor_id" class="form-select">

                            <option value="">
                                -- Pilih Surveyor --
                            </option>

                            @foreach($surveyors as $surveyor)

                                <option
                                    value="{{ $surveyor->id }}"
                                    {{ old('surveyor_id') == $surveyor->id ? 'selected' : '' }}
                                >
                                    {{ $surveyor->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('surveyor_id')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('survey.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button class="btn btn-success">Simpan Penugasan</button>
                </div>
                </form>
            
            </div>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection

@section('scripts')

<script>
$(function () {

    function toggleSurveyor() {

        let jenis = $('#jenis').val();

        if (jenis === 'assign') {

            $('#surveyor-area').slideDown(200);

            $('#surveyor_id').prop('required', true);

        } else {

            $('#surveyor-area').slideUp(200);

            $('#surveyor_id')
                .prop('required', false)
                .val('');

        }
    }

    toggleSurveyor();

    $('#jenis').on('change', function () {
        toggleSurveyor();
    });

});
</script>

@endsection