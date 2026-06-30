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
                    <div class="card-header bg-azure text-azure-fg">
                        <h3 class="card-title">DATA DOKUMEN</h3>
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
            <a href="{{ route('pengajuan.analisa',$pengajuan->id) }}"class="btn btn-success">
                Lanjut Analisa
            </a>
        </div>
      </div>
    </div>
    
</div>
@endsection
