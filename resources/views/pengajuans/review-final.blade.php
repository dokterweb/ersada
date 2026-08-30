@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-12">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">STEP 1</li>
                            <li class="step-item">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item">ANALISA</li>
                            <li class="step-item">JAMINAN</li>
                            <li class="step-item">ANALISA KAPITAL</li>
                            <li class="step-item active">REVIEW FINAL</li>
                        </ul>
        
                    </div>
                </div>
            </div>
                  
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">DATA PENGAJUAN</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>No Pengajuan</td>
                                <td>{{ $pengajuan->nomor_pengajuan }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
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
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">DATA NASABAH</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>Nama</td>
                                <td>{{ $pengajuan->nasabah->nama }}</td>
                            </tr>
                            <tr>
                                <td>NIK</td>
                                <td>{{ $pengajuan->nasabah->nik }}</td>
                            </tr>
                            <tr>
                                <td>No HP</td>
                                <td>{{ $pengajuan->nasabah->no_hp }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-lime text-lime-fg">
                        <h3 class="card-title">DATA NASABAH DAN PEKERJAAN</h3>
                    </div>
                    <div class="card-body">
                       <div class="row">
                           <div class="col-md-6">
                                <div class="card mt-3 p-3">
                                    <h2>Data Nasabah</h2>
                                    <table class="table">
                                        <tr>
                                            <td>Nama</td>
                                            <td>{{ $pengajuan->nasabah->nama }}</td>
                                        </tr>
                                        <tr>
                                            <td>NIK</td>
                                            <td>{{ $pengajuan->nasabah->nik }}</td>
                                        </tr>
                                        <tr>
                                            <td>No HP</td>
                                            <td>{{ $pengajuan->nasabah->no_hp }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tempat Lahir</td>
                                            <td>{{ $pengajuan->nasabah->tempat_lahir }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tgl Lahir</td>
                                            {{-- <td>{{ $pengajuan->nasabah->tgl_lahir }}</td> --}}
                                            <td>{{ \Carbon\Carbon::parse($pengajuan->nasabah->tgl_lahir)->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Alamat</td>
                                            <td>{{ $pengajuan->nasabah->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <td>Status Perkawinan</td>
                                            <td>{{ $pengajuan->nasabah->status_perkawinan }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jlh Tanggungan</td>
                                            <td>{{ $pengajuan->nasabah->jumlah_tanggungan }}</td>
                                        </tr>
                                        <tr>
                                            <td>Status Rumah</td>
                                            <td>{{ $pengajuan->nasabah->status_rumah }}</td>
                                        </tr>
                                        <tr>
                                            <td>Lama Menetap Tahun</td>
                                            <td>{{ $pengajuan->nasabah->lama_menetap_tahun }} tahun</td>
                                        </tr>
                                        <tr>
                                            <td>Lama Menetap Bulan</td>
                                            <td>{{ $pengajuan->nasabah->lama_menetap_bulan }} bulan</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mt-3 p-3">
                                    <h2>Data Pekerjaan Nasabah</h2>
                                    <table class="table">
                                        <tr>
                                            <td>Jenis Pekerjaan</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->jenis_pekerjaan }}</td>
                                        </tr>
                                        <tr>
                                            <td>Penghasilan</td>
                                            <td>{{ number_format($pengajuan->nasabah->pekerjaanNasabah->penghasilan) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->nama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jenis Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->jenis_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Lama Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->lama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Pegawai</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->jumlah_pegawai }}</td>
                                        </tr>
                                        <tr>
                                            <td>Alamat Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->alamat_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Telp Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->telpon_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Bangunan Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->bangunan_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Status Tempat Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->status_tempat_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aktifitas Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaanNasabah->aktivitas_usaha }}</td>
                                        </tr>
                                       
                                    </table>
                                </div>
                            </div>
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

            @if($saudaras->count())
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-azure text-azure-fg">
                        <h3 class="card-title">DATA PENJAMIN DAN PEKERJAAN</h3>
                    </div>
                    <div class="card-body">
                       <div class="row">
                           @foreach($saudaras as $saudara)
                            <div class="col-md-6">
                                <div class="card mt-3 p-3">
                                    <h2>Data Saudara {{ $loop->iteration }}</h2>
                                    <table class="table">
                                        <tr>
                                            <td>Nama</td>
                                            <td>{{ $saudara->nama }}</td>
                                        </tr>
                                        <tr>
                                            <td>No HP</td>
                                            <td>{{ $saudara->no_hp }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tempat Lahir</td>
                                            <td>{{ $saudara->tempat_lahir }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tgl Lahir</td>
                                            {{-- <td>{{ $saudara->tgl_lahir }}</td> --}}
                                            <td>{{ \Carbon\Carbon::parse($saudara->tgl_lahir)->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Alamat</td>
                                            <td>{{ $saudara->alamat }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @endforeach
                       </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white d-flex justify-content-between">
                        <h3 class="card-title">DATA DOKUMEN</h3>
                        @if($mode == 'review')
                            <a href="{{ route('pengajuan.step4', $pengajuan->id) }}"class="btn btn-light">Edit</a>
                        @endif
                    </div>
                    <div class="card-body">
                       <div class="row">
                        <table class="table">
                            @foreach($pengajuan->dokumenPengajuans as $doc)
                                <tr>
                                    <td>{{ $doc->jenis_label  }}</td>
                                    <td>
                                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">{{ $doc->nama_file }}</a>
                                    </td>
                                    <td>{{ $doc->status }}</td>
                                </tr>
                            @endforeach
                        </table>
                       </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================
                JAMINAN
            ========================================================= --}}
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

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white d-flex justify-content-between">
                        <h3 class="card-title">HASIL ANALISA</h3>
                        @if($mode == 'review')
                            <a href="{{ route('pengajuan.analisa',$pengajuan->id) }}" class="btn btn-light">Edit</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                        @php
                            $analisa = $pengajuan->analisa;
                        @endphp

                        @if($analisa)
                            <div class="row">
                                {{-- KOLOM KIRI --}}
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <strong>Harga Kredit</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->harga_kredit,'expected'=>1,'label'=>'Setuju'])&nbsp;&nbsp;&nbsp;
                                        @include('layouts.checklist',['value'=>$analisa->harga_kredit,'expected'=>0,'label'=>'Tidak Setuju'])
                                    </div>
                                    <div class="mb-4">
                                        <strong>Kewajiban Angsuran</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->kewajiban_angsuran,'expected'=>1,'label'=>'Sudah Dijelaskan'])&nbsp;&nbsp;&nbsp;
                                        @include('layouts.checklist',['value'=>$analisa->kewajiban_angsuran,'expected'=>0,'label'=>'Belum'])
                                    </div>
                                    <div class="mb-4">
                                        <strong>Status Pemohon</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->status_pemohon,'expected'=>'pemilik','label'=>'Pemilik'])
                                        @include('layouts.checklist',['value'=>$analisa->status_pemohon,'expected'=>'karyawan','label'=>'Karyawan'])
                                        @include('layouts.checklist',['value'=>$analisa->status_pemohon,'expected'=>'pengelola','label'=>'Pengelola'])
                                    </div>
                                </div>
                                {{-- KOLOM TENGAH --}}
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <strong>Status Tempat Tinggal</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->status_tempat_tinggal,'expected'=>1,'label'=>'Milik'])&nbsp;&nbsp;&nbsp;
                                        @include('layouts.checklist',['value'=>$analisa->status_tempat_tinggal,'expected'=>0,'label'=>'Kontrak'])
                                    </div>
                                    <div class="mb-4">
                                        <strong>Data Pemohon</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->data_pemohon_lengkap,'expected'=>1,'label'=>'Lengkap'])&nbsp;&nbsp;&nbsp;
                                        @include('layouts.checklist',['value'=>$analisa->data_pemohon_lengkap,'expected'=>0,'label'=>'Tidak Lengkap'])
                                    </div>
                                    <div class="mb-3">
                                        <strong>KTP Pemohon</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->ktp_pemohon_valid,'expected'=>1,'label'=>'Valid'])&nbsp;&nbsp;&nbsp;
                                        @include('layouts.checklist',['value'=>$analisa->ktp_pemohon_valid,'expected'=>0,'label'=>'Tidak Valid'])
                                    </div>
                                </div>
                                {{-- KOLOM KANAN --}}
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <strong>KTP Pasangan</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->ktp_pasangan_valid,'expected'=>1,'label'=>'Valid'])&nbsp;&nbsp;&nbsp;
                                        @include('layouts.checklist',['value'=>$analisa->ktp_pasangan_valid,'expected'=>0,'label'=>'Tidak Valid'])
                                    </div>
                                    <div class="mb-3">
                                        <strong>Kartu Keluarga</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->kk_valid,'expected'=>1,'label'=>'Valid'])&nbsp;&nbsp;&nbsp;
                                        @include('layouts.checklist',['value'=>$analisa->kk_valid,'expected'=>0,'label'=>'Tidak Valid'])
                                    </div>
                                    <div>
                                        <strong>Perbaikan Plafon</strong><br>
                                        @include('layouts.checklist',['value'=>$analisa->kk_valid,'expected'=>1,'label'=>'Sudah'])&nbsp;&nbsp;&nbsp;
                                        @include('layouts.checklist',['value'=>$analisa->kk_valid,'expected'=>0,'label'=>'Belum'])
                                    </div>
                                </div>
                            </div>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white d-flex justify-content-between">
                        <h3 class="card-title">
                            DATA JAMINAN
                        </h3>
                        @if($mode == 'review')
                        <a href="{{ route('pengajuan.jaminan', $pengajuan->id) }}"
                           class="btn btn-light">
                            Edit
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                       <div class="row">
                            @if($pengajuan->jaminanPengajuans->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th width="20%">Jenis Jaminan</th>
                                                <th width="25%">Nama Jaminan / Agunan</th>
                                                <th>Detail Jaminan Yang Diagunkan</th>
                                                <th width="20%">Taksiran Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pengajuan->jaminanPengajuans as $jaminan)
                                            <tr>
                                                <td>{{ $jaminan->jenis_jaminan }}</td>
                                                <td>{{ $jaminan->nama_jaminan }}</td>
                                                <td>{{ $jaminan->detail_jaminan }}</td>
                                                <td class="text-end">Rp {{ number_format($jaminan->nilai_taksiran,0,',','.') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    Data jaminan belum diinput.
                                </div>
                            @endif
                       </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-12">
                <div class="card">
                   <div class="card-header bg-info text-white d-flex justify-content-between">
                        <h3 class="card-title">
                            ANALISA KAPITAL
                        </h3>
                        @if($mode == 'review')
                        <a href="{{ route('pengajuan.kapital',$pengajuan->id) }}"
                           class="btn btn-light">
                            Edit
                        </a>
                        @endif
                    </div>

                    <div class="card-body">
                       <div class="row justify-content-center">
                            <div class="col-10">
                            @php
                                $kapital = $pengajuan->kapital;
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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">KONFIRMASI PENGAJUAN</h3>
                    </div>
                    <div class="card-body">
                       <div class="row">
                            @if($mode == 'review')
                            <form action="{{ route('pengajuan.reviewFinal.submit', $pengajuan->id) }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Catatan Marketing</label>
                                        <textarea name="catatan_marketing" rows="5"
                                            class="form-control @error('catatan_marketing') is-invalid @enderror"
                                            placeholder="Tambahkan catatan apabila diperlukan..."
                                        >{{ old('catatan_marketing', $pengajuan->catatan_marketing) }}</textarea>
                                        @error('catatan_marketing')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('confirm_submit') is-invalid @enderror"
                                            type="checkbox" value="1" id="confirm_submit" name="confirm_submit">
                                        <label class="form-check-label" for="confirm_submit">
                                            Saya memastikan seluruh data pengajuan, dokumen, analisa,
                                            jaminan dan kapital telah diperiksa serta sudah benar.
                                        </label>
                                        @error('confirm_submit')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                    </div>
                        
                                </div>
                        
                                <div class="card-footer d-flex justify-content-between">
                                    <button
                                        type="submit" formaction="{{ route('pengajuan.reviewFinal.save',$pengajuan->id) }}" class="btn btn-secondary">
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        Simpan Draft
                                    </button>
                                    <button
                                        type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        Kirim ke Pimpinan
                                    </button>
                                </div>
                            </form>
                            @elseif($mode == 'approval')
                                <form action="{{ route('pimpinan.submit',$pengajuan->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Catatan Marketing</label>
                                        <div class="border rounded p-3 bg-light">
                                            {!! nl2br(e($pengajuan->catatan_marketing)) !!}
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <label class="form-label">Catatan Pimpinan</label>
                                        <textarea name="catatan" rows="4" class="form-control" placeholder="Tuliskan keputusan pimpinan..."></textarea>
                                    </div>
                                    <div class="mb-4">

                                        <label class="form-label">Keputusan</label>
                                    
                                        {{-- Tahap 1 --}}
                                        @if($pengajuan->status == 'menunggu_pimpinan')
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="aksi" value="survey">
                                                <label class="form-check-label">Lanjutkan Proses Survey</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="aksi" value="tolak">
                                                <label class="form-check-label">Tolak Pengajuan</label>
                                            </div>
                                    
                                        {{-- Tahap 2 --}}
                                        @elseif($pengajuan->status == 'menunggu_keputusan')
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="aksi" value="setujui" {{ old('aksi') == 'setujui' ? 'checked' : '' }}>
                                                 <label class="form-check-label">
                                                    
                                                <label class="form-check-label">Setujui Pembiayaan</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input"type="radio"name="aksi"value="tolak" {{ old('aksi') == 'tolak' ? 'checked' : '' }}>
                                                <label class="form-check-label">Tolak Pengajuan</label>
                                            </div>

                                            <div id="plafond-box" style="display:none">
                                                <div class="row">
                                            
                                                    {{-- Plafond Pengajuan --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Plafond Pengajuan</label>
                                                        <input type="text" class="form-control" readonly
                                                            value="Rp {{ number_format($pengajuan->nominal_pengajuan,0,',','.') }}">
                                                    </div>
                                                    {{-- Plafond Disetujui --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Plafond Disetujui</label>
                                                        <input type="text" id="plafond_disetujui" name="plafond_disetujui" class="form-control rupiah"
                                                            value="{{ old('plafond_disetujui', number_format($pengajuan->nominal_pengajuan,0,',','.')) }}">
                                                        <small class="text-muted">Nominal yang disetujui pimpinan.</small>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    {{-- Tenor Pengajuan --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Tenor Pengajuan</label>
                                                        <input type="text" class="form-control" readonly value="{{ $pengajuan->tenor }} Bulan">
                                                    </div>
                                                    {{-- Tenor Disetujui --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Tenor Disetujui</label>
                                                        <input type="number" name="tenor_disetujui" class="form-control" min="1"
                                                            value="{{ old('tenor_disetujui',$pengajuan->tenor) }}">
                                                        <small class="text-muted">
                                                            Lama angsuran yang disetujui.
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button class="btn btn-success">
                                        <i class="fa fa-check-circle"></i>
                                        Simpan Keputusan
                                    </button>
                                </form>
                            @else
                             {{-- Tampilan Read Only --}}
                            <div class="card-body">
                                <label class="form-label">Catatan Marketing</label>
                                <div class="border rounded p-3 bg-light">
                                    {!! nl2br(e($pengajuan->catatan_marketing)) !!}
                                </div>
                            </div>
                            @endif
                        </table>
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
    $(function () {
    
        // Jalankan saat halaman pertama kali dibuka
        togglePlafond();
    
        // Jalankan ketika pilihan aksi berubah
        $('input[name="aksi"]').on('change', function () {
            togglePlafond();
        });
    
        // Tampilkan / sembunyikan form plafond
        function togglePlafond() {
            let aksi = $('input[name="aksi"]:checked').val();
            if (aksi === 'setujui') {
                $('#plafond-box').stop(true, true).slideDown();
            } else {
                $('#plafond-box').stop(true, true).slideUp();
            }
    
        }
    });

    function formatRupiah(value) {
        let angka = value.toString().replace(/[^0-9]/g, '');
        if (angka === '') return '';
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    $('.rupiah').on('input', function () {
        $(this).val(formatRupiah($(this).val()));
    });

    // Format juga saat halaman pertama kali dibuka
    $('.rupiah').each(function () {
        $(this).val(formatRupiah($(this).val()));
    });

</script>
    
@endsection