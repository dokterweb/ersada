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
                            <li class="step-item active">REVIEW</li>
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
                                            <td>{{ $pengajuan->nasabah->tgl_lahir }}</td>
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
                                            <td>{{ $pengajuan->nasabah->pekerjaan->jenis_pekerjaan }}</td>
                                        </tr>
                                        <tr>
                                            <td>Penghasilan</td>
                                            <td>{{ number_format($pengajuan->nasabah->pekerjaan->penghasilan) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->nama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jenis Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->jenis_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Lama Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->lama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Pegawai</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->jumlah_pegawai }}</td>
                                        </tr>
                                        <tr>
                                            <td>Alamat Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->alamat_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Telp Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->telpon_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Bangunan Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->bangunan_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Status Tempat Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->status_tempat_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aktifitas Usaha</td>
                                            <td>{{ $pengajuan->nasabah->pekerjaan->aktivitas_usaha }}</td>
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
                                            <td>{{ $pasangan->tgl_lahir }}</td>
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
                                            <td>{{ $penjamin->tgl_lahir }}</td>
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
                                            <td>{{ $saudara->tgl_lahir }}</td>
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
                        <h3 class="card-title">
                            DATA DOKUMEN
                        </h3>
                        <a href="{{ route('pengajuan.step4', $pengajuan->id) }}"
                           class="btn btn-light">
                            Edit
                        </a>
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
            <div class="col-md-12">
                <div class="card">
                    
                    <div class="card-header bg-info text-white d-flex justify-content-between">
                        <h3 class="card-title">
                            HASIL ANALISA
                        </h3>
                        @if($mode == 'review')
                        <a href="{{ route('pengajuan.analisa',$pengajuan->id) }}"
                           class="btn btn-light">
                            Edit
                        </a>
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
            <div class="col-md-12">
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
                        @if($pengajuan->jaminans->count())
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
                                        @foreach($pengajuan->jaminans as $jaminan)
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
            </div>
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
                    
                                    <h4 class="text-primary">Pendapatan</h4>
                    
                                    <table class="table table-sm">
                    
                                        <tr>
                                            <td>Omzet Harian</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->omzet_harian) }}
                                            </td>
                                        </tr>
                    
                                        <tr>
                                            <td>Laba Harian</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->laba_harian) }}
                                            </td>
                                        </tr>
                    
                                        <tr>
                                            <td>Pendapatan Lain</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->pendapatan_lain) }}
                                            </td>
                                        </tr>
                    
                                        <tr>
                                            <td>Pendapatan Pasangan</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->pendapatan_pasangan) }}
                                            </td>
                                        </tr>
                    
                                    </table>
                    
                                </div>
                    
                                {{-- PENGELUARAN --}}
                                <div class="col-md-6">
                    
                                    <h4 class="text-danger">Pengeluaran</h4>
                    
                                    <table class="table table-sm">
                    
                                        <tr>
                                            <td>Rumah Tangga</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->biaya_rumah_tangga) }}
                                            </td>
                                        </tr>
                    
                                        <tr>
                                            <td>Motor</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->biaya_motor) }}
                                            </td>
                                        </tr>
                    
                                        <tr>
                                            <td>Koperasi</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->biaya_koperasi) }}
                                            </td>
                                        </tr>
                    
                                        <tr>
                                            <td>Angsuran Lain</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->angsuran_lain) }}
                                            </td>
                                        </tr>
                    
                                        <tr>
                                            <td>Kontrak Rumah</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->biaya_kontrak_rumah) }}
                                            </td>
                                        </tr>
                    
                                        <tr>
                                            <td>Tempat Usaha</td>
                                            <td class="text-end">
                                                Rp {{ number_format($kapital->biaya_tempat_usaha) }}
                                            </td>
                                        </tr>
                    
                                    </table>
                    
                                </div>
                    
                            </div>
                    
                            <hr>
                    
                            <div class="row">
                    
                                <div class="col-md-6">
                    
                                    <h4>Total Pengeluaran</h4>
                    
                                    <h2 class="text-danger">
                    
                                        Rp {{ number_format($kapital->total_pengeluaran) }}
                    
                                    </h2>
                    
                                </div>
                    
                                <div class="col-md-6 text-end">
                    
                                    <h4>Sisa Pendapatan</h4>
                    
                                    <h2 class="text-success">
                    
                                        Rp {{ number_format($kapital->sisa_pendapatan) }}
                    
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
                                        Kirim ke SPV Marketing
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
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="aksi" id="tolak" value="tolak">
                                            <label class="form-check-label" for="tolak">Tolak Pengajuan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="aksi" value="survey">
                                            <label>Lanjutkan Proses Survey</label>
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
